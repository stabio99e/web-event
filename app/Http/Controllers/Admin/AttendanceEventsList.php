<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventsTicketType;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceEventsList extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->roles !== 'admin') {
            redirect()->route('dashboard')->send();
        }
    }

    public function index(Request $request, $eventId)
    {
        $event = Event::with(['ticketTypes', 'EventsLocation'])->findOrFail($eventId);

        $participants = DB::table('tickets as t')
            ->join('order_items as oi', 't.order_item_id', '=', 'oi.id')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('events_ticket_types as ett', 'oi.ticket_type_id', '=', 'ett.id')
            ->join('users as u', 'o.user_id', '=', 'u.id')
            ->where('o.event_id', $eventId)
            ->where('o.status', 'PAID')
            ->select(['t.id as ticket_id', 't.ticket_number', 'oi.ticket_type_id', 't.attendee_name as name', 't.attendee_email as email', 'u.phone as phone', 'ett.name as category', DB::raw("COALESCE(t.attendance_status, 'pending') as status"), 't.checkin_time', 't.attendance_note as note'])
            ->get();

        $totalParticipants = $participants->count();
        $presentCount = $participants->where('status', 'present')->count();
        $absentCount = $participants->where('status', 'absent')->count();

        $ticketTypes = EventsTicketType::where('event_id', $eventId)->get();

        return view('admin.attendance.index', compact('event', 'participants', 'totalParticipants', 'presentCount', 'absentCount', 'ticketTypes'));
    }

    public function updateAttendance(Request $request, $eventId, $ticketId)
    {
        $request->validate([
            'status' => 'required|in:present,absent,pending',
            'note' => 'nullable|string',
        ]);

        $ticket = Ticket::where('id', $ticketId)
            ->whereHas('orderItem.order', function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })
            ->firstOrFail();

        // Debugging: Tambahkan log ini
        Log::info('Updating ticket', [
            'ticket_id' => $ticketId,
            'status' => $request->status,
            'note' => $request->note,
        ]);

        $updateResult = $ticket->update([
            'attendance_status' => $request->status,
            'attendance_note' => $request->note,
            'checkin_time' => $request->status === 'present' ? now() : null,
        ]);

        Log::info('Update result', ['success' => $updateResult]);

        return response()->json([
            'success' => $updateResult,
            'message' => $updateResult ? 'Attendance updated successfully' : 'Failed to update attendance',
        ]);
    }
}
