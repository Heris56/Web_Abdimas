<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIP = [
            '180.245.142.27',
            '127.0.0.1'

        ];
        if (!in_array($request->ip(), $allowedIP)) {
            session()->flush();
            \Cookie::queue(\Cookie::forget('userID'));
            \Cookie::queue(\Cookie::forget('userRole'));
            \Cookie::queue(\Cookie::forget('primarykey'));
            return abort(403, 'Akses admin hanya diizinkan dari IP tertentu.');
        }

        return $next($request);
    }
}
