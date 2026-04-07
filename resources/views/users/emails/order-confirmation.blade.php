@component('mail::message')
# Terima kasih atas pesanan Anda!

Pesanan Anda dengan nomor **#{{ $order->order_number }}** telah berhasil diproses.

**Detail Event:**  
{{ $order->event->title }}  
{{ $order->event->start_datetime->translatedFormat('l, d F Y') }}  
{{ $order->event->start_datetime->format('H:i') }} - {{ $order->event->end_datetime->format('H:i') }}  
{{ $order->event->EventsLocation->name }}

**Detail Pembayaran:**  
Total Pembayaran: Rp{{ number_format($order->grand_total, 0, ',', '.') }}  
Metode Pembayaran: {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}  
Tanggal Pembayaran: {{ $order->paid_at->translatedFormat('l, d F Y, H:i') }}

@component('mail::button', ['url' => route('profile.orders')])
Lihat Pesanan Saya
@endcomponent

**Tiket Anda:**  
@foreach($order->tickets as $ticket)
- {{ $ticket->attendee_name }} ({{ $ticket->ticket_number }})
@endforeach

Anda dapat mengunduh tiket Anda melalui tombol di bawah ini:

@component('mail::button', ['url' => route('tickets.download', ['order' => $order]), 'color' => 'success'])
Download E-Ticket
@endcomponent

Terima kasih,  
{{ config('app.name') }}
@endcomponent