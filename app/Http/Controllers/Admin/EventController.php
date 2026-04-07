<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventsLocation;
use App\Models\EventsTicketType;
use App\Models\Pages;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->roles !== 'admin') {
            redirect()->route('dashboard')->send();
        }
    }

    public function index()
    {
        $events = Event::with('EventsLocation')->where('status', 1)->latest('start_datetime')->get();
        $locations = EventsLocation::selectRaw('city, MAX(id) as max_id')->whereNotNull('city')->groupBy('city')->orderByDesc('max_id')->limit(5)->get();
        return view('admin.events.index', compact('events', 'locations'));
    }

    public function show(Request $request, $eventId)
    {
        $event = Event::with(['EventsLocation', 'ticketTypes'])->findOrFail($eventId);

        $search = $request->query('search');
        $status = $request->query('status');

        $orders = Order::where('event_id', $eventId)->with('transaction')->get();

        $paidOrders = $orders->filter(fn($o) => $o->status === 'PAID' && optional($o->transaction)->status === 'PAID');
        $unpaidOrders = $orders->filter(fn($o) => $o->status === 'UNPAID');

        $paidOrderIds = $paidOrders->pluck('id');
        $orderItems = OrderItem::whereIn('order_id', $paidOrderIds)->get();
        $orderItemIds = $orderItems->pluck('id');

        $ppnwithOrder = $paidOrders->sum(fn($o) => $o->ppn_fee ?? 0);
        $totalRevenue = $orderItems->sum(fn($item) => $item->price * $item->quantity) + $ppnwithOrder;

        // Query awal tiket
        $ticketsQuery = Ticket::with(['orderItem.order', 'orderItem.ticketType'])->whereIn('order_item_id', $orderItemIds);

        // Filter search
        if ($search) {
            $ticketsQuery->where(function ($q) use ($search) {
                $q->where('attendee_name', 'like', "%$search%")->orWhere('attendee_email', 'like', "%$search%");
            });
        }

        // Filter status
        if ($status === 'paid') {
            $ticketsQuery->whereHas('orderItem.order', fn($q) => $q->where('status', 'PAID'));
        } elseif ($status === 'pending') {
            $ticketsQuery->whereHas('orderItem.order', fn($q) => $q->where('status', 'UNPAID'));
        }

        // Ambil transaksi berdasarkan order dari event terkait
        $transactions = Transaction::with(['order.user'])
            ->whereHas('order', fn($q) => $q->where('event_id', $eventId))
            ->latest()
            ->get();

        // Untuk jumlah total transaksi
        $transactionCount = $transactions->count();

        $tickets = $ticketsQuery
            ->latest()
            ->paginate(10)
            ->appends(request()->query());

        $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $registrationChart = [];

        // Ambil data tiket per hari dalam minggu ini (7 hari ke belakang)
        foreach ($weekDays as $day) {
            $count = Ticket::whereHas('orderItem.order', function ($q) use ($eventId) {
                $q->where('event_id', $eventId)->where('status', 'PAID');
            })
                ->whereRaw('DAYNAME(created_at) = ?', [Carbon::parse($day)->format('l')])
                ->count();

            $registrationChart[$day] = $count;
        }

        $registrationChart = Ticket::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->whereHas('orderItem.order', function ($q) use ($eventId) {
                $q->where('event_id', $eventId)->where('status', 'PAID');
            })
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn($item) => [Carbon::parse($item->date)->translatedFormat('D') => $item->total])
            ->toArray();

        $ticketTypes = EventsTicketType::where('event_id', $eventId)
            ->withCount([
                'tickets as sold' => function ($q) {
                    $q->whereHas('orderItem.order', fn($o) => $o->where('status', 'PAID'));
                },
            ])
            ->get();

        $totalSold = $ticketTypes->sum('sold');
        $event->load([
            'ticketTypes' => function ($q) {
                $q->withCount([
                    'tickets as sold' => function ($q) {
                        $q->whereHas('orderItem.order', fn($q) => $q->where('status', 'PAID'));
                    },
                ]);
            },
        ]);

        return view('admin.events.show', [
            'event' => $event,
            'totalAttendees' => $tickets->total(),
            'totalRevenue' => $totalRevenue,
            'paidCount' => $paidOrders->count(),
            'unpaidCount' => $unpaidOrders->count(),
            'participantPercent' => $event->max_attendees ? round(($tickets->total() / $event->max_attendees) * 100, 1) : 0,
            'paidPercent' => $orders->count() ? round(($paidOrders->count() / $orders->count()) * 100, 1) : 0,
            'unpaidPercent' => $orders->count() ? round(($unpaidOrders->count() / $orders->count()) * 100, 1) : 0,
            'tickets' => $tickets,
            'search' => $search,
            'filterStatus' => $status,
            'transactions' => $transactions,
            'transactionCount' => $transactionCount,
            'registrationChart' => $registrationChart,
            'ticketTypes' => $ticketTypes,
            'totalSold' => $totalSold,
        ]);
    }
    // public function edit($eventId)
    // {
    //     $event = Event::with('eventsLocation')->findOrFail($eventId);
    //     $locations = EventsLocation::selectRaw('city, MAX(id) as max_id')->whereNotNull('city')->groupBy('city')->orderByDesc('max_id')->limit(5)->get();

    //     return view('admin.events.edit', compact('event', 'locations'));
    // }

    public function edit($eventId)
    {
        $event = Event::with(['eventsLocation', 'ticketTypes'])->findOrFail($eventId);
        $locations = EventsLocation::selectRaw('city, MAX(id) as max_id')->whereNotNull('city')->groupBy('city')->orderByDesc('max_id')->limit(5)->get();

        return view('admin.events.edit', compact('event', 'locations'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'slug' => 'required|string|max:255|unique:events,slug,' . $id,
    //         'description' => 'nullable|string',
    //         'content' => 'nullable|string',
    //         'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'start_datetime' => 'required|date',
    //         'end_datetime' => 'required|date|after_or_equal:start_datetime',
    //         'status' => 'required|in:1,2,3,4',
    //         'link_group' => 'nullable|url',
    //     ]);

    //     $event = Event::findOrFail($id);
    //     $event->title = $request->title;
    //     $event->slug = $request->slug;
    //     $event->description = $request->description;
    //     $event->content = $request->content;
    //     $event->start_datetime = $request->start_datetime;
    //     $event->end_datetime = $request->end_datetime;
    //     $event->status = $request->status;
    //     $event->url_group = $request->link_group;

    //     if ($request->hasFile('image_path')) {
    //         $file = $request->file('image_path');
    //         $filename = time() . '_' . $file->getClientOriginalName();

    //         // Tentukan path manual ke folder publik (rindutenang.id/storage/events)
    //         $publicRoot = base_path('../rindutenang.id');
    //         $targetPath = $publicRoot . '/storage/events';

    //         // Buat folder jika belum ada
    //         if (!file_exists($targetPath)) {
    //             mkdir($targetPath, 0755, true);
    //         }

    //         // Hapus gambar lama jika ada
    //         if ($event->image_path) {
    //             $oldFile = $publicRoot . str_replace('/storage', '/storage', $event->image_path);
    //             if (file_exists($oldFile)) {
    //                 unlink($oldFile);
    //             }
    //         }
    //         $file->move($targetPath, $filename);
    //         $event->image_path = '/storage/events/' . $filename;
    //     }

    //     $event->save();

    //     return redirect()
    //         ->route('admin.events.details', ['eventsid' => $id])
    //         ->with('success', 'Event berhasil diperbarui.');
    // }

    public function update(Request $request, $eventId)
    {
        // Validasi data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after:start_datetime',
            'max_attendees' => 'nullable|integer|min:0',
            'location_name' => 'required|string|max:255',
            'location_address' => 'required|string',
            'location_city' => 'required|string|max:100',
            'location_province' => 'required|string|max:100',
            'location_country' => 'nullable|string|max:100',
            'location_map_url' => 'nullable|url',
            'link_group' => 'nullable|url',
            'ticket_types' => 'required|array|min:2|max:4',
            'ticket_types.*.name' => 'required|string|max:100',
            'ticket_types.*.description' => 'nullable|string|max:255',
            'ticket_types.*.price' => 'required|numeric|min:0',
            'ticket_types.*.quantity_available' => 'required|integer|min:0',
            'ticket_types.*.is_premium' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $event = Event::findOrFail($eventId);

            // Update event data
            $event->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'content' => $validated['content'],
                'start_datetime' => $validated['start_datetime'],
                'end_datetime' => $validated['end_datetime'],
                'max_attendees' => $validated['max_attendees'] ?? null,
                'link_group' => $validated['link_group'] ?? null,
            ]);

            // Update or create location
            if ($event->eventsLocation) {
                $event->eventsLocation->update([
                    'name' => $validated['location_name'],
                    'address' => $validated['location_address'],
                    'city' => $validated['location_city'],
                    'province' => $validated['location_province'],
                    'country' => $validated['location_country'] ?? 'Indonesia',
                    'map_url' => $validated['location_map_url'] ?? null,
                ]);
            } else {
                EventsLocation::create([
                    'event_id' => $event->id,
                    'name' => $validated['location_name'],
                    'address' => $validated['location_address'],
                    'city' => $validated['location_city'],
                    'province' => $validated['location_province'],
                    'country' => $validated['location_country'] ?? 'Indonesia',
                    'map_url' => $validated['location_map_url'] ?? null,
                ]);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($event->image) {
                    Storage::delete('public/events/' . $event->image);
                }

                $imageName = time() . '.' . $request->image->extension();
                $request->image->storeAs('public/events', $imageName);
                $event->image = $imageName;
                $event->save();
            }

            // Update ticket types
            $existingTicketIds = $event->ticketTypes->pluck('id')->toArray();
            $updatedTicketIds = [];

            foreach ($validated['ticket_types'] as $ticketData) {
                if (isset($ticketData['id'])) {
                    // Update existing ticket
                    $ticket = EventsTicketType::find($ticketData['id']);
                    if ($ticket) {
                        $ticket->update([
                            'name' => $ticketData['name'],
                            'description' => $ticketData['description'] ?? null,
                            'price' => $ticketData['price'],
                            'quantity_available' => $ticketData['quantity_available'],
                            'is_premium' => $ticketData['is_premium'] ?? false,
                        ]);
                        $updatedTicketIds[] = $ticket->id;
                    }
                } else {
                    // Create new ticket
                    $ticket = EventsTicketType::create([
                        'event_id' => $event->id,
                        'name' => $ticketData['name'],
                        'description' => $ticketData['description'] ?? null,
                        'price' => $ticketData['price'],
                        'quantity_available' => $ticketData['quantity_available'],
                        'is_premium' => $ticketData['is_premium'] ?? false,
                    ]);
                    $updatedTicketIds[] = $ticket->id;
                }
            }

            // Delete tickets that were removed
            $ticketsToDelete = array_diff($existingTicketIds, $updatedTicketIds);
            if (!empty($ticketsToDelete)) {
                EventsTicketType::whereIn('id', $ticketsToDelete)->delete();
            }

            DB::commit();

            return redirect()
                ->route('admin.events.details', ['eventsid' => $eventId])
                ->with('success', 'Event berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui event: ' . $e->getMessage());
        }
    }

    public function updateSettings(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer',
            'max_attendees' => 'required|integer|min:0',
        ]);

        $event = Event::with('ticketTypes')->findOrFail($id);

        DB::transaction(function () use ($request, $event) {
            $event->status = $request->status;
            $event->max_attendees = $request->max_attendees;
            $event->save();

            foreach ($request->ticket_types as $ticketData) {
                $ticketType = EventsTicketType::find($ticketData['id']);
                if ($ticketType && $ticketType->event_id == $event->id) {
                    $ticketType->price = $ticketData['price'];
                    $ticketType->quantity_available = $ticketData['quantity_available'];
                    $ticketType->is_active = isset($ticketData['is_active']) ? (bool) $ticketData['is_active'] : false;
                    $ticketType->save();
                }
            }
        });

        return redirect()->back()->with('success', 'Pengaturan event berhasil diperbarui.');
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after:start_datetime',
            'max_attendees' => 'nullable|integer|min:0',
            'link_group' => 'nullable|url',

            // Lokasi
            'location_name' => 'required|string|max:255',
            'location_address' => 'required|string',
            'location_city' => 'required|string|max:100',
            'location_province' => 'required|string|max:100',
            'location_country' => 'nullable|string|max:100',
            'location_map_url' => 'nullable|url',

            // Tiket
            'ticket_types' => 'required|array|min:2|max:4',
            'ticket_types.*.name' => 'required|string|max:255',
            'ticket_types.*.description' => 'nullable|string',
            'ticket_types.*.price' => 'required|numeric|min:0',
            'ticket_types.*.quantity_available' => 'required|integer|min:0',
            'ticket_types.*.is_premium' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            Log::info('Validation passed');

            $imagePath = null;

            $publicRoot = public_path();

            $storageFolder = public_path('storage/events');

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = 'event_' . time() . '.' . $image->getClientOriginalExtension();

                if (!file_exists($storageFolder)) {
                    mkdir($storageFolder, 0755, true);
                }

                $image->move($storageFolder, $filename);

                $imagePath = '/storage/events/' . $filename;

                Log::info("Image uploaded: $imagePath");
            }
            // Generate ID unik
            do {
                $id = random_int(100000, 999999);
            } while (Event::where('id', $id)->exists());

            Log::info("Generated Event ID: $id");

            $event = Event::create([
                'id' => $id,
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'content' => $request->input('content'),
                'image_path' => $imagePath,
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $request->end_datetime,
                'max_attendees' => $request->max_attendees ?? 0,
                'status' => 1,
                'url_group' => $request->link_group ?? null,
            ]);

            Log::info("Event created: {$event->id}");

            // Simpan lokasi
            EventsLocation::create([
                'event_id' => $event->id,
                'name' => $request->location_name,
                'address' => $request->location_address,
                'city' => $request->location_city,
                'province' => $request->location_province,
                'country' => $request->location_country ?? 'Indonesia',
                'map_url' => $request->location_map_url,
            ]);

            Log::info("Event location saved for event ID: {$event->id}");

            // Simpan tiket
            $ticketTypesData = [];

            foreach ($request->ticket_types as $ticket) {
                $ticketTypesData[] = [
                    'event_id' => $event->id,
                    'name' => $ticket['name'],
                    'description' => $ticket['description'] ?? null,
                    'price' => $ticket['price'],
                    'quantity_available' => $ticket['quantity_available'],
                    'is_premium' => !empty($ticket['is_premium']),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            EventsTicketType::insert($ticketTypesData);

            Log::info("All tickets saved for event ID: {$event->id}");

            DB::commit();
            Log::info('Event successfully stored');

            return redirect()->route('admin.events.show')->with('success', 'Event berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating event: ' . $e->getMessage());

            if ($imagePath) {
                $fullPath = $publicRoot . $imagePath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat event.');
        }
    }
}
