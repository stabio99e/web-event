<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;

class CertificateUsersController extends Controller
{
    public function form()
    {
        return view('certificates.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string',
        ]);

        // Cari ticket dan event langsung dari tiket number
        $ticket = DB::table('tickets')->join('order_items', 'tickets.order_item_id', '=', 'order_items.id')->join('orders', 'order_items.order_id', '=', 'orders.id')->join('events', 'orders.event_id', '=', 'events.id')->where('tickets.ticket_number', $request->ticket_number)->select('tickets.attendee_name as participant_name', 'tickets.ticket_number', 'events.id as event_id', 'events.title as event_title')->first();

        if (!$ticket) {
            Log::warning('Certificate NOT FOUND for ticket: ' . $request->ticket_number);
            return response()->json(['message' => 'Tiket tidak ditemukan untuk event ini.'], 404);
        }

        $template = DB::table('certificate_templates')->where('event_id', $ticket->event_id)->where('is_active', true)->first();

        if (!$template) {
            return response()->json(['message' => 'Sertifikat belum tersedia untuk event ini.'], 404);
        }

        $templatePath = public_path($template->image_path);
        if (!file_exists($templatePath)) {
            return response()->json(['message' => 'Template sertifikat tidak ditemukan.'], 404);
        }

        $imageManager = new ImageManager(new Driver());
        $image = $imageManager->read($templatePath);

        $fontPath = $template->font_path ? public_path($template->font_path) : null;
        if ($fontPath && !file_exists($fontPath)) {
            $fontPath = null;
        }

        $x = strtolower($template->position_x) === 'auto' ? $image->width() / 2 : (int) $template->position_x;

        $image->text($ticket->participant_name, $x, $template->position_y, function ($font) use ($template, $fontPath) {
            if ($fontPath) {
                $font->filename($fontPath);
            }
            $font->size($template->font_size);
            $font->color($template->text_color ?? '#000000');
            $font->align('center');
            $font->valign('middle');
        });

        // Convert ke base64
        $tempPath = tempnam(sys_get_temp_dir(), 'cert_') . '.png';
        $image->save($tempPath);
        $base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($tempPath));
        unlink($tempPath);

        Log::info('Generate certificate for ticket: ' . $request->ticket_number);

        return response()->json([
            'participant_name' => $ticket->participant_name,
            'certificate_base64' => $base64,
            'event_title' => $ticket->event_title,
        ]);
    }
}
