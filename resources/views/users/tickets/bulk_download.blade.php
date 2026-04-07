<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>E-Ticket</title>
    <style>
        @page {
            size: A4;
            margin: 5mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .tickets-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5mm;
            padding: 5mm;
        }

        .ticket {
            position: relative;
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 60mm;
            border: 1px solid #e2e8f0;
            border-radius: 3mm;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .ticket-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            opacity: 0.05;
            /* transparan */
            z-index: 0;
            /* di belakang konten */
            pointer-events: none;
            /* tidak ganggu klik/scan */
        }

        .ticket-info {
            flex: 3;
            padding: 4mm;
            display: flex;
            flex-direction: column;
        }

        .ticket-qr {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #f8fafc;
            border-left: 1px dashed #cbd5e1;
            padding: 2mm;
        }

        .event-title {
            font-size: 12px;
            font-weight: bold;
            color: #0d9488;
            margin-bottom: 2mm;
            text-align: center;
        }

        .ticket-number {
            font-size: 10px;
            background-color: #0d9488;
            color: #f8fafc;
            padding: 1mm 2mm;
            border-radius: 2mm;
            display: inline-block;
            margin-bottom: 3mm;
            text-align: center;
        }

        .info-row {
            margin-bottom: 2mm;
            font-size: 9px;
            display: grid;
            grid-template-columns: 20mm 1fr;
        }

        .info-label {
            color: #64748b;
            font-weight: bold;
        }

        .info-value {
            color: #334155;
        }

        .divider {
            border-top: 1px dashed #e2e8f0;
            margin: 3mm 0;
        }

        .qr-code {
            width: 30mm;
            height: 30mm;
            object-fit: contain;
        }


        .verification-text {
            font-size: 8px;
            color: #64748b;
            text-align: center;
            margin-top: 2mm;
        }

        .footer {
            font-size: 7px;
            color: #64748b;
            text-align: center;
            margin-top: auto;
            padding-top: 2mm;
        }
    </style>
</head>

<body>
    @foreach ($tickets as $ticket)
        <div class="tickets-container">
            <div class="ticket">
                <img src="{{ public_path('storage/images/logo.png') }}" class="ticket-watermark" />
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <!-- Kiri: Biodata -->
                        <td style="width:60%; padding:4mm; vertical-align:top;">
                            <div class="event-title">{{ $ticket->orderItem->order->event->title }}</div>
                            <div class="ticket-number">#{{ $ticket->ticket_number }}</div>

                            <div class="info-row"><span class="info-label">Nama:</span> {{ $ticket->attendee_name }}
                            </div>
                            <div class="info-row"><span class="info-label">Tanggal:</span>
                                {{ \Carbon\Carbon::parse($ticket->orderItem->order->event->start_datetime)->translatedFormat('d/m/Y') }}
                            </div>
                            <div class="info-row"><span class="info-label">Waktu:</span>
                                {{ \Carbon\Carbon::parse($ticket->orderItem->order->event->start_datetime)->format('H:i') }}-{{ \Carbon\Carbon::parse($ticket->orderItem->order->event->end_datetime)->format('H:i') }}
                            </div>
                            <div class="info-row"><span class="info-label">Lokasi:</span>
                                {{ $ticket->orderItem->order->event->EventsLocation->name }}a</div>

                            <div class="info-row"><span class="info-label">Jenis Tiket:</span>
                                {{ $ticket->orderItem->ticketType->name ?? 'Jenis tiket tidak ditemukan' }}

                                <div class="divider"></div>
                                <div class="footer">
                                    <p
                                        style="font-size: 10px; margin-top: -10px; padding-bottom: 10px; line-height: 1.2; text-align: center;">
                                        <strong>Gunakan tiket ini untuk masuk ke ruangan acara. Tunjukkan tiket ini di
                                            pintu masuk.</strong><br>
                                        Pembelian tiket hanya dilakukan melalui website rindutenang.id
                                    </p>

                                    © {{ date('Y') }} {{ $webConfig->site_name ?? 'Eventsku' }}
                                </div>

                        </td>
                        <td
                            style="width:40%; padding:4mm; background:#f8fafc; border-left:1px dashed #cbd5e1; text-align:center;">
                            <img src="{{ $ticket->getQrCodeBase64() }}" class="qr-code"
                                style="width:30mm; height:30mm;"><br>
                            <div class="verification-text">Scan QR code untuk verifikasi</div>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    @endforeach

</body>

</html>
