<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Order;

class TransactionController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->roles !== 'admin') {
            redirect()->route('dashboard')->send();
        }
    }

    public function index(Request $request)
    {
        $query = Transaction::with(['order.user'])->latest();

        if ($request->filled('q')) {
            $search = $request->q;
            $request->validate([
                'q' => 'nullable|string|max:255',
            ]);
            $query
                ->whereHas('order.user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('order', function ($q) use ($search) {
                    $q->where('order_number', 'like', '%' . $search . '%');
                });
        }

        $transactions = $query->paginate(15);
        return view('admin.transaction.index', compact('transactions'));
    }

    public function edit($orderID)
    {
        $order = Order::with('transaction')->findOrFail($orderID);
        $statusOptions = ['PAID', 'FAILED', 'EXPIRED', 'UNPAID', 'REFUND'];

        return view('admin.transaction.edit', compact('order', 'statusOptions'));
    }

    public function update(Request $request, $orderID)
    {
        $request->validate([
            'status' => 'required|in:PAID,FAILED,EXPIRED,UNPAID,REFUND',
        ]);

        $order = Order::findOrFail($orderID);
        $transaction = Transaction::where('order_id', $orderID)->firstOrFail();

        $order->status = $request->status;
        $order->save();

        $transaction->status = $request->status;
        $transaction->save();

        return redirect()->route('admin.transaction.edit', $orderID)->with('success', 'Status berhasil diperbarui di kedua tabel.');
    }
}
