<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Ticket;

class CallBackTripay extends Controller
{
    public function handle(Request $request)
    {
        $privateKey = 'KehHt-BVYSF-ylKAR-ahs0Q-78OWs';

        $json = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $json, $privateKey);
        $callbackSignature = $request->header('X-Callback-Signature');

        if ($signature !== $callbackSignature) {
            Log::warning('Signature tidak valid dari Tripay Callback');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $data = json_decode($json, true);
        $merchantRef = $data['merchant_ref'] ?? null;
        $status = $data['status'] ?? null;

        if (!$merchantRef || !$status) {
            Log::error('Data callback tidak lengkap', $data);
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $order = Order::where('order_number', $merchantRef)->first();
        if (!$order) {
            Log::error('Order tidak ditemukan berdasarkan merchant_ref: ' . $merchantRef);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transaction = Transaction::where('order_id', $order->id)->first();
        if (!$transaction) {
            Log::error('Transaksi tidak ditemukan untuk order_id: ' . $order->id);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Update status transaksi dan order
            $transaction->update(['status' => $status]);
            $order->update(['status' => $status]);

            // Jika status REFUND, kembalikan saldo ke user
            if ($status === 'REFUND') {
                $amount = $data['amount_received'] ?? 0;

                if ($amount > 0) {
                    $user = $order->user;

                    if ($user) {
                        $user->increment('saldo', $amount);
                        Log::info("Saldo dikembalikan ke user ID {$user->id} sejumlah {$amount}");
                    } else {
                        Log::warning("User tidak ditemukan untuk order ID {$order->id}");
                    }
                } else {
                    Log::warning("Amount refund tidak valid untuk merchant_ref {$merchantRef}");
                }
            }

            // Kirim email invoice berdasarkan status
            $this->sendInvoiceEmail($order, $status);

            DB::commit();

            Log::info('Callback Tripay sukses update status', [
                'merchant_ref' => $merchantRef,
                'status' => $status,
            ]);

            return response()->json(['message' => 'Status updated'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memproses callback Tripay: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to process callback'], 500);
        }
    }

    protected function sendInvoiceEmail($order, $status)
    {
        try {
            $recipient = $order->user->email;
            $sender = 'rindutenang@mailsry.web.id';
            $eventTitle = $order->event->title;
            $totalBayar = number_format($order->TotalPayAmount + $order->admin_fee, 0, ',', '.');
            $orderNumber = $order->order_number;
            $userName = $order->user->name;

            $subject = 'Status Pembayaran Event #' . $orderNumber;
            $header = 'Status Pembayaran Anda';
            $message = "<p>Status pembayaran Anda untuk event <strong>$eventTitle</strong> saat ini adalah: <strong>$status</strong>.</p>";
            $closing = '<p>Jika Anda merasa ini adalah kesalahan, silakan hubungi tim kami.</p>';

            $attachments = [];

            switch (strtoupper($status)) {
                case 'PAID':
                    $subject = 'Pembayaran Berhasil - Tiket Event #' . $orderNumber;
                    $header = 'Pembayaran Berhasil';

                    $tickets = Ticket::whereIn('order_item_id', function ($query) use ($order) {
                        $query->select('id')->from('order_items')->where('order_id', $order->id);
                    })
                        ->with('orderItem.order.event.EventsLocation', 'orderItem.order.event.ticketTypes')
                        ->get();

                    $pdf = Pdf::loadView('users.tickets.bulk_download', compact('tickets'));
                    $pdfContent = $pdf->output();
                    $pdfBase64 = base64_encode($pdfContent);

                    $attachments[] = [
                        'name' => "Tiket_Order_{$orderNumber}.pdf",
                        'content_type' => 'application/pdf',
                        'data' => $pdfBase64,
                    ];

                    $message = "
                        <p>Terima kasih! Pembayaran Anda untuk event <strong>$eventTitle</strong> telah berhasil kami terima.</p>
                        <p><strong>Order Number:</strong> $orderNumber</p>
                        <p><strong>Total Pembayaran:</strong> Rp $totalBayar</p>
                        <p>Tiket Anda terlampir dalam email ini dalam bentuk PDF.</p>
                    ";
                    $closing = '<p>Sampai jumpa di event!</p>';
                    break;

                case 'EXPIRED':
                    $subject = 'Pembayaran Kadaluwarsa - Event #' . $orderNumber;
                    $header = 'Pembayaran Kadaluwarsa';
                    $message = "
                        <p>Sayang sekali, pembayaran Anda untuk event <strong>$eventTitle</strong> telah <strong>kadaluwarsa</strong>.</p>
                        <p>Silakan lakukan pemesanan ulang jika masih berminat.</p>";
                    $closing = '<p>Terima kasih atas perhatian Anda.</p>';
                    break;

                case 'FAILED':
                    $subject = 'Pembayaran Gagal - Event #' . $orderNumber;
                    $header = 'Pembayaran Gagal';
                    $message = "
                        <p>Mohon maaf, pembayaran Anda untuk event <strong>$eventTitle</strong> <strong>gagal</strong> diproses.</p>
                        <p>Silakan coba kembali atau gunakan metode pembayaran lain.</p>";
                    $closing = '<p>Jika Anda butuh bantuan, hubungi tim kami.</p>';
                    break;

                case 'REFUND':
                    $subject = 'Pengembalian Dana - Event #' . $orderNumber;
                    $header = 'Dana Telah Dikembalikan';
                    $message = "
                        <p>Transaksi Anda untuk event <strong>$eventTitle</strong> telah <strong>dikembalikan</strong>.</p>
                        <p>Dana sejumlah Rp $totalBayar telah dikreditkan ke saldo Anda.</p>";
                    $closing = '<p>Terima kasih telah menggunakan layanan kami.</p>';
                    break;

                default:
                    $subject = 'Status Pembayaran Event #' . $orderNumber;
                    $header = 'Informasi Status Pembayaran';
                    $message = "<p>Status pembayaran Anda saat ini adalah: <strong>$status</strong>.</p>";
                    $closing = '<p>Hubungi kami jika Anda membutuhkan bantuan lebih lanjut.</p>';
                    break;
            }

            // Email content
            $htmlBody = "
                <h2>$header</h2>
                <p>Halo <strong>$userName</strong>,</p>
                $message
                $closing
                <br>
                <p>Salam hangat,<br>Tim Rindutenang</p>
            ";

            $plainText = strip_tags(str_replace('<br>', "\n", $htmlBody));

            // Kirim email ke Mailry API
            $response = \Http::withHeaders([
                'X-Server-API-Key' => 'IvpI6Y9OnzVcQWkcu44OziSF',
                'Accept' => 'application/json',
            ])->post('https://mailry.fachry.dev/api/v1/send/message', [
                'to' => $recipient,
                'from' => $sender,
                'subject' => $subject,
                'plain_body' => $plainText,
                'html_body' => $htmlBody,
                'attachments' => $attachments, // << kirim PDF-nya
            ]);

            if (!$response->successful()) {
                Log::warning('Gagal mengirim email invoice', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email invoice: ' . $e->getMessage());
        }
    }
}
