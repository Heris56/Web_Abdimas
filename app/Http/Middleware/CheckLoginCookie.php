<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLoginCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userID = $request->cookie('userID');
        $userRole = $request->cookie('userRole');

        if (!$userID || !$userRole) {
            return redirect()->route('landing')->with('warning', 'Silakan login terlebih dahulu');
        }
        return $next($request);
    }
}
