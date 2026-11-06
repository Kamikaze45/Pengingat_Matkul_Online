<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip(); // ambil IP pengunjung
        $today = now()->format('Y-m-d');

        // Cek apakah IP sudah tercatat hari ini
        $exists = DB::table('visitors')
            ->where('ip_address', $ip)
            ->whereDate('visited_at', $today)
            ->exists();

        if (!$exists) {
            DB::table('visitors')->insert([
                'ip_address' => $ip,
                'visited_at' => now(),
            ]);
        }

        return $next($request);
    }
}
