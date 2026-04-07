<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visitor;
use Carbon\Carbon;

class LogVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $today = Carbon::today();

        // Cek apakah IP ini sudah tercatat hari ini
        $exists = Visitor::where('ip', $request->ip())->whereDate('visited_date', $today)->exists();

        if (!$exists) {
            Visitor::create([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'visited_date' => $today,
            ]);
        }

        return $next($request);
    }
}
