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

        ];
        if (!in_array($request->ip(), $allowedIP)) {
            return abort(403, 'Akses admin hanya diizinkan dari IP tertentu.');
        }

        return $next($request);
    }
}
