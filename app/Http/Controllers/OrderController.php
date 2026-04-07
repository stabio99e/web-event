<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\EventsTicketType;
use App\Models\Ticket;
use App\Models\ChannelPay;
use App\Models\Transaction;
use App\Models\OrderItem;
use App\Models\EventsLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create(Request $request, Event $event)
    {
        $ticketData = $request->query('tickets', []);

        if (!is_array($ticketData)) {
            $ticketData = [];
        }

        $selectedTickets = [];
        foreach ($ticketData as $ticketId => $quantity) {
            if ($quantity > 0) {
                $ticketType = EventsTicketType::findOrFail($ticketId);
                $selectedTickets[$ticketId] = [
                    'quantity' => (int) $quantity,
                    'ticket_type' => $ticketType,
                ];
            }
        }

        if (empty($selectedTickets)) {
            return redirect()->back()->with('error', 'Pilih minimal 1 tiket');
        }

        return view('users.orders.create', [
            'event' => $event,
            'selectedTickets' => $selectedTickets,
        ]);
    }

    public function validateAttendees(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $validated = $request->validate([
            'tickets' => 'required|array',
            'tickets.*.quantity' => 'required|integer|min:1',
            'tickets.*.attendees' => 'nullable|array',
            'tickets.*.attendees.*.name' => 'sometimes|required|string|max:255',
            'tickets.*.attendees.*.email' => 'required|email|max:255',
        ]);

        $validationErrors = $this->validateStockAndQuota($event, $validated['tickets']);
        if ($validationErrors) {
            return redirect()->back()->with('error', $validationErrors);
        }

        Session::put('pending_order', [
            'event_id' => $event->id,
            'tickets' => $validated['tickets'],
            'total_amount' => $this->calculateTotal($validated['tickets']),
        ]);

        return redirect()->route('orders.payment-form', $event);
    }

    public function paymentForm(Event $event)
    {
        if (!Session::has('pending_order')) {
            return redirect()->route('events.show', $event);
        }

        $pendingOrder = Session::get('pending_order');

        $tempOrder = (object) [
            'event' => $event,
            'total_amount' => $pendingOrder['total_amount'],
            'admin_fee' => 0,
            'grand_total' => $pendingOrder['total_amount'],
            'items' => $this->createTempOrderItems($pendingOrder['tickets']),
        ];

        $channelsByGroup = ChannelPay::where('status', 'active')->select('id', 'channel_code', 'channel_name', 'channel_group', 'biaya_flat', 'biaya_percent', 'ppn')->get()->groupBy('channel_group');

        return view('users.orders.payment', [
            'order' => $tempOrder,
            'event' => $event,
            'channelsByGroup' => $channelsByGroup,
        ]);
    }

    private function createTempOrderItems(array $tickets)
    {
        $items = [];
        foreach ($tickets as $ticketTypeId => $ticketData) {
            $ticketType = EventsTicketType::find($ticketTypeId);
            if ($ticketType) {
                $items[] = (object) [
                    'ticketType' => $ticketType,
                    'quantity' => $ticketData['quantity'],
                    'price' => $ticketType->price,
                ];
            }
        }
        return collect($items);
    }

    public function processPayment(Request $request, Event $event)
    {
        if (!Session::has('pending_order')) {
            return redirect()->route('events.show', $event);
        }

        $validated = $request->validate([
            'channel_code' => 'required|exists:channel_pay,channel_code',
        ]);

        $channel = ChannelPay::where('channel_code', $validated['channel_code'])->firstOrFail();
        $pendingOrder = Session::get('pending_order');

        try {
            $order = DB::transaction(function () use ($event, $pendingOrder, $channel) {
                $user = auth()->user();

                $event = Event::where('id', $event->id)->lockForUpdate()->firstOrFail();

                $validationErrors = $this->validateStockAndQuota($event, $pendingOrder['tickets']);
                if ($validationErrors) {
                    throw new \Exception($validationErrors);
                }

                // Hitung biaya dasar
                $subtotal = $pendingOrder['total_amount']; // total harga tiket
                $flat = $channel->biaya_flat ?? 0;
                $percent = $channel->biaya_percent ?? 0;

                // Admin fee internal
                $adminFee = $flat + round($subtotal * ($percent / 100));

                // Hitung PPN (jika tidak ada, maka 0)
                $ppnRate = is_numeric($channel->ppn) ? $channel->ppn : 0;
                $ppn = round($adminFee * ($ppnRate / 100));

                // Grand total sistem 
                $grandTotalUI = $subtotal + $ppn;

                // Simpan Order
                $order = Order::create([
                    'id' => random_int(100000, 999999),
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'order_number' => 'EVT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                    'TotalPayAmount' => $grandTotalUI,
                    'total_amount' => $subtotal,
                    'admin_fee' => $adminFee,
                    'ppn_fee' => $ppn,
                    'payment_method' => $channel->channel_code,
                    'status' => 'UNPAID',
                    'paid_at' => now(),
                ]);

                // Ambil ticket type
                $ticketTypeIds = array_keys($pendingOrder['tickets']);
                $ticketTypes = EventsTicketType::whereIn('id', $ticketTypeIds)->lockForUpdate()->get()->keyBy('id');

                // Siapkan items[] untuk Tripay
                $orderItems = collect($ticketTypeIds)->map(function ($id) use ($ticketTypes, $pendingOrder) {
                    $type = $ticketTypes[$id];
                    return [
                        'sku' => 'TIKET-' . $id,
                        'name' => $type->name,
                        'price' => (int) $type->price,
                        'quantity' => (int) $pendingOrder['tickets'][$id]['quantity'],
                        'product_url' => route('events.show', $type->event_id),
                    ];
                });

                // Tambahkan PPN ke items jika ada
                if ($ppn > 0) {
                    $orderItems->push([
                        'sku' => 'PPN',
                        'name' => 'PPN 11%',
                        'price' => $ppn,
                        'quantity' => 1,
                    ]);
                }

                $orderItems = $orderItems->values()->all();

                // Kirim ke Tripay
                $tripay = $this->createTripayTransaction(
                    [
                        'amount' => $subtotal + $ppn,
                        'customer_name' => $user->name,
                        'customer_email' => $user->email,
                        'customer_phone' => $user->phone ?? '0811309902',
                        'items' => $orderItems,
                    ],
                    $channel,
                    $order,
                );

                // Simpan transaksi
                Transaction::create([
                    'order_id' => $order->id,
                    'reference' => $tripay['reference'],
                    'merchant_ref' => $tripay['merchant_ref'],
                    'payment_method' => $tripay['payment_method'],
                    'payment_name' => $tripay['payment_name'],
                    'customer_name' => $tripay['customer_name'],
                    'customer_email' => $tripay['customer_email'],
                    'customer_phone' => $tripay['customer_phone'],
                    'pay_code' => $tripay['pay_code'] ?? ($tripay['qr_url'] ?? ($tripay['pay_url'] ?? null)),
                    'checkout_url' => $tripay['checkout_url'],
                    'status' => $tripay['status'],
                    'amount' => $tripay['amount'],
                    'fee_merchant' => $tripay['fee_merchant'],
                    'fee_customer' => $tripay['fee_customer'],
                    'amount_received' => $tripay['amount_received'],
                    'expired_time' => now()->setTimestamp($tripay['expired_time']),
                    'raw_response' => $tripay,
                ]);

                // Generate tiket
                foreach ($pendingOrder['tickets'] as $ticketTypeId => $ticketData) {
                    $ticketType = $ticketTypes[$ticketTypeId];

                    $orderItem = $order->orderItems()->create([
                        'ticket_type_id' => $ticketType->id,
                        'quantity' => $ticketData['quantity'],
                        'price' => $ticketType->price,
                    ]);

                    $ticketsData = [];

                    if ($ticketData['quantity'] == 1) {
                        $ticketsData[] = [
                            'ticket_number' => 'TKT-' . Str::upper(Str::random(10)),
                            'order_item_id' => $orderItem->id,
                            'user_id' => $user->id,
                            'attendee_name' => $user->name,
                            'attendee_email' => $user->email,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {
                        foreach ($ticketData['attendees'] as $attendee) {
                            $ticketsData[] = [
                                'ticket_number' => 'TKT-' . Str::upper(Str::random(10)),
                                'order_item_id' => $orderItem->id,
                                'user_id' => $attendee['email'] === $user->email ? $user->id : null,
                                'attendee_name' => $attendee['name'],
                                'attendee_email' => $attendee['email'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    Ticket::insert($ticketsData);
                }

                $this->sendInvoiceEmail($order);

                return $order;
            });
        } catch (\Exception $e) {
            Log::error('Payment Processing Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
        }

        Session::forget('pending_order');
        return redirect()->route('orders.pay', $order);
    }

    public function complete(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }
        if ($order->status !== 'PAID') {
            return redirect()->route('dashboard');
        }

        return view('users.orders.complete', compact('order'));
    }

    private function validateStockAndQuota(Event $event, array $tickets)
    {
        $event = Event::where('id', $event->id)->lockForUpdate()->first();
        $totalRegistered = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.event_id', $event->id)
            ->whereIn('orders.status', ['PAID', 'UNPAID'])
            ->sum('order_items.quantity');

        $totalRequested = collect($tickets)->sum(fn($ticket) => $ticket['quantity']);

        if ($totalRegistered + $totalRequested > $event->max_attendees) {
            $sisa = max(0, $event->max_attendees - $totalRegistered);
            return 'Maaf, kuota peserta sudah habis.';
        }

        foreach ($tickets as $ticketTypeId => $ticketData) {
            $ticketType = EventsTicketType::where('id', $ticketTypeId)->lockForUpdate()->first();
            $totalSold = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.event_id', $event->id)
                ->where('order_items.ticket_type_id', $ticketType->id)
                ->whereIn('orders.status', ['PAID', 'UNPAID'])
                ->sum('order_items.quantity');

            $requested = $ticketData['quantity'];

            if ($totalSold + $requested > $ticketType->quantity_available) {
                $sisa = max(0, $ticketType->quantity_available - $totalSold);
                return "Maaf, sisa tiket {$ticketType->name} sudah habis. Silakan kurangi jumlah atau pilih tiket lain.";
            }
        }

        return null;
    }

    private function calculateTotal(array $tickets)
    {
        $total = 0;

        foreach ($tickets as $ticketTypeId => $ticketData) {
            $ticketType = EventsTicketType::findOrFail($ticketTypeId);
            $total += $ticketType->price * $ticketData['quantity'];
        }

        return $total;
    }

    public function paynow(Request $request, $orderid)
    {
        $order = Order::with(['event.EventsLocation', 'items.ticketType', 'transaction'])
            ->where('id', $orderid)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $transaction = $order->transaction;
        if (strtoupper($order->status) === 'PAID' && strtoupper($transaction->status) === 'PAID') {
            return redirect()->route('orders.complete', ['order' => $order->id]);
        }
        if (strtoupper($order->status) !== 'UNPAID' || strtoupper($transaction->status) !== 'UNPAID') {
            return redirect()->route('dashboard');
        }
        $orderItems = $order->items;
        $event = $order->event;

        return view('users.orders.paynow', compact('transaction', 'order', 'event', 'orderItems'));
    }
    public function cancel(Request $request, $orderId)
    {
        $order = Order::with('transaction')
            ->where('id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status !== 'UNPAID') {
            return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah diproses.');
        }

        DB::beginTransaction();
        try {
            $order->status = 'CANCELED';
            $order->save();
            if ($order->transaction) {
                $order->transaction->status = 'CANCELED';
                $order->transaction->save();
            }
            DB::commit();
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan pesanan. Silakan coba lagi.');
        }
    }
    private function createTripayTransaction(array $orderData, $channel, Order $order)
    {
        $apiKey = 'DEV-aPnPPlEYl6oeH874AR0RYqsGqjz45yHYgpE8aKnT';
        $privateKey = 'KehHt-BVYSF-ylKAR-ahs0Q-78OWs';
        $merchantCode = 'T42113';
        $merchantRef = $order->order_number;

        $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $orderData['amount'], $privateKey);

        $data = [
            'method' => $channel->channel_code,
            'merchant_ref' => $merchantRef,
            'amount' => $orderData['amount'],
            'customer_name' => $orderData['customer_name'],
            'customer_email' => $orderData['customer_email'],
            'customer_phone' => $orderData['customer_phone'],
            'order_items' => $orderData['items'],
            'return_url' => route('orders.complete', $order),
            'expired_time' => now()->addHours(24)->timestamp,
            'signature' => $signature,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])
            ->asForm()
            ->post('https://tripay.co.id/api-sandbox/transaction/create', $data);

        if (!$response->successful()) {
            throw new \Exception('Gagal membuat transaksi: ' . $response->body());
        }

        return $response->json('data');
    }
    protected function sendInvoiceEmail($order)
    {
        try {
            $recipient = $order->user->email;
            $sender = 'rindutenang@mailsry.web.id';

            $subject = 'Invoice Pembayaran Event: ' . $order->order_number;

            $plainText = "Halo {$order->user->name},\n\nTerima kasih atas pemesanan Anda untuk event \"{$order->event->title}\". Berikut ini adalah detail invoice Anda:\n\nOrder Number: {$order->order_number}\nTotal: Rp " . number_format($order->TotalPayAmount, 0, ',', '.') . "\n\nSilakan lakukan pembayaran sebelum waktu yang ditentukan.\n\nSalam,\nTim Rindutenang.";

            $htmlBody =
                "
            <h2>Invoice Pembayaran</h2>
            <p>Halo {$order->user->name},</p>
            <p>Terima kasih atas pemesanan Anda untuk event <strong>{$order->event->title}</strong>.</p>
            <p><strong>Order Number:</strong> {$order->order_number}</p>
            <p><strong>Total Bayar:</strong> Rp " .
                number_format($order->TotalPayAmount + $order->admin_fee, 0, ',', '.') .
                "</p>
            <p>Silakan lakukan pembayaran sebelum <strong>" .
                $order->transaction->expired_time->format('d M Y H:i') .
                "</strong>.</p>
            <p><a href=\"{$order->transaction->checkout_url}\">Klik di sini untuk membayar</a></p>
            <p>Salam,<br>Tim Rindutenang</p>
        ";

            $response = Http::withHeaders([
                'X-Server-API-Key' => 'IvpI6Y9OnzVcQWkcu44OziSF',
                'Accept' => 'application/json',
            ])->post('https://mailry.fachry.dev/api/v1/send/message', [
                'to' => $recipient,
                'from' => $sender,
                'subject' => $subject,
                'plain_body' => $plainText,
                'html_body' => $htmlBody,
            ]);

            if (!$response->successful()) {
                \Log::warning('Gagal mengirim email invoice', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email invoice: ' . $e->getMessage());
        }
    }
}
