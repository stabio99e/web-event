<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeroSlider;
use App\Models\Event;
use App\Models\EventsLocation;
use App\Models\EventsTicketType;
use App\Models\Pages;
use App\Models\OrderItem;
use App\Models\Qna;

/**
 * HomeController handles the display of the home page and event listings and view details events and details pages
 */
class HomeController extends Controller
{
    public function index()
    {
        $events = Event::with('EventsLocation')->where('status', 1)->latest('id')->get();
        $sliders = HeroSlider::where('is_active', true)->orderBy('sort_order')->get();
        $locations = EventsLocation::selectRaw('city, MAX(id) as max_id')->whereNotNull('city')->groupBy('city')->orderByDesc('max_id')->limit(5)->get();
        return view('users.index', compact('events', 'sliders', 'locations'));
    }

    public function ListEvents()
    {
        $events = Event::with('EventsLocation')->where('status', 1)->latest('id')->get();
        $locations = EventsLocation::selectRaw('city, MAX(id) as max_id')->whereNotNull('city')->groupBy('city')->orderByDesc('max_id')->limit(5)->get();
        return view('users.content.list', compact('events', 'locations'));
    }

    public function Show($slug)
    {
        $getQna = Qna::get();
        $event = Event::with([
            'EventsLocation',
            'ticketTypes' => function ($query) {
                $query->where('is_active', true);
            },
        ])
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $ticketTypes = $event->ticketTypes;
        $usedQuantities = OrderItem::select('ticket_type_id', \DB::raw('SUM(quantity) as used'))
            ->whereHas('order', function ($q) use ($event) {
                $q->where('event_id', $event->id)->where('status', 'PAID');
            })
            ->groupBy('ticket_type_id')
            ->pluck('used', 'ticket_type_id');

        foreach ($ticketTypes as $ticketType) {
            $ticketType->used_quantity = $usedQuantities[$ticketType->id] ?? 0;
        }

        $recommendedEvents = Event::with('EventsLocation')
            ->whereHas('EventsLocation', function ($query) use ($event) {
                if ($event->EventsLocation) {
                    $query->where('city', $event->EventsLocation->city);
                }
            })
            ->where('id', '!=', $event->id)
            ->where('status', 1)
            ->where('start_datetime', '>', now())
            ->orderBy('start_datetime', 'asc')
            ->limit(3)
            ->get();

        return view('users.content.show', compact('event', 'recommendedEvents', 'getQna'));
    }
    public function PagesShow($slug)
    {
        $page = Pages::where('slug', $slug)->where('is_published', 1)->firstOrFail();
        if ($page) {
            return view('users.content.pages', [
                'page' => $page,
            ]);
        } else {
            abort(404);
        }
    }
}
