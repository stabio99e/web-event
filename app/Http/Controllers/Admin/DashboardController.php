<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->roles !== 'admin') {
            redirect()->route('dashboard')->send();
        }
    }
    public function index()
    {
        $revenue = Order::where('status', 'PAID')
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'PAID');
            })
            ->sum('TotalPayAmount');
        $revenuePPN = Order::where('status', 'PAID')
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'PAID');
            })
            ->sum('ppn_fee');
        $totalOrders = Order::count();
        $totalEvents = Event::count();
        $totalUsers = User::where('roles', 'user')->count();

        $latestTransactions = Transaction::with(['order.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('revenue', 'revenuePPN', 'totalOrders', 'totalEvents', 'totalUsers', 'latestTransactions'));
    }

    public function chartVisitor(Request $request)
    {
        $type = $request->get('type', 'daily');

        if ($type === 'monthly') {
            $visitors = Visitor::select(DB::raw("DATE_FORMAT(visited_date, '%Y-%m') as label"), DB::raw('COUNT(*) as count'))->groupBy('label')->orderBy('label', 'asc')->limit(12)->get();
        } else {
            $visitors = Visitor::select(DB::raw('DATE(visited_date) as label'), DB::raw('COUNT(*) as count'))
                ->where('visited_date', '>=', Carbon::now()->subDays(30))
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        }

        return response()->json($visitors);
    }
}
