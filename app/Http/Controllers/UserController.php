<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\EventsTicketType;
use App\Models\Transaction;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\EventsLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $orders = Order::with(['event.EventsLocation', 'items.ticketType', 'transaction'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $countAll = $orders->count();
        $countSuccess = $orders->where('status', 'PAID')->count();
        $countPending = $orders->where('status', 'UNPAID')->count();
        $countCancelled = $orders->whereIn('status', ['EXPIRED', 'FAILED', 'REFUND'])->count();
        $users = User::where('id', auth()->id())->first();
        return view('users.content.users', compact('orders', 'countAll', 'countSuccess', 'countPending', 'countCancelled', 'users'));
    }
    public function show($orderID)
    {
        $CheckOrderID = Order::where('id', $orderID)->where('status', 'PAID')->first();
        if (!$CheckOrderID) {
            abort(404);
        }
        return redirect()->route('orders.complete', ['order' => $orderID]);
    }
    public function profile()
    {
        $user = auth()->user();
        return view('users.content.users-profile', compact('user'));
    }
    public function updateFormPhone()
    {
        $user = auth()->user()->phone;
        if ($user === null) {
            return view('users.content.users-update-phone');
        } else {
            return redirect()->route('users.profile')->with('error', 'Nomor WhatsApp sudah terdaftar.');
        }
    }
    public function updatePhone(Request $request)
    {
        $user = auth()->user()->phone;
        if ($user === null) {
            $request->validate([
                'phone' => ['required', 'numeric', 'digits_between:10,15', 'regex:/^(08|62)[0-9]{8,13}$/'],
            ]);

            $user = auth()->user();
            $user->phone = $request->phone;
            $user->save();

            return back()->with('success', 'Nomor WhatsApp berhasil diperbarui.');
        } else {
            return redirect()->route('users.profile')->with('error', 'Nomor WhatsApp sudah terdaftar.');
        }
    }
}
