<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPeminjam
{
    public function handle(Request $request, Closure $next): Response 
    { 
        if ($request->user() && $request->user()->is_active !== false && $request->user()->role === 'peminjam') {
            return $next($request); 
        } 
        
        return response()->json(['message' => 'Akses ditolak. Anda bukan Peminjam.'], 403); 
    } 
}
