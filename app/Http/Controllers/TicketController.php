<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\EventsTicketType;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function downloadAll(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        $transaction = Transaction::where('order_id', $order->id)->first();
        if (!$transaction || $order->status !== 'PAID' || $transaction->status !== 'PAID') {
            abort(403, 'Transaksi belum berhasil. Tiket belum tersedia.');
        }
        $tickets = Ticket::whereIn('order_item_id', function ($query) use ($order) {
            $query->select('id')->from('order_items')->where('order_id', $order->id);
        })
            ->with('orderItem.order.event.EventsLocation', 'orderItem.order.event.ticketTypes')
            ->get();

        // Generate PDF
        $pdf = PDF::loadView('users.tickets.bulk_download', compact('tickets'));
        return $pdf->stream("tickets-order-{$order->order_number}.pdf");
    }
}
