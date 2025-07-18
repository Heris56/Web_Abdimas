<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Jenssegers\Agent\Agent;
use App\Http\Controllers\login_controller;

class BlockUserOnMobileWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $userRole = session("userRole") ?? $request->cookie('userRole');

        $agent = new Agent();
        $isMobile = $agent->isMobile();

        $blockedRoles = ['staff', 'wali_kelas', 'guru_mapel'];

        if ($isMobile && in_array($userRole, $blockedRoles)) {
            session()->flush();
            \Cookie::queue(\Cookie::forget('userID'));
            \Cookie::queue(\Cookie::forget('userRole'));
            \Cookie::queue(\Cookie::forget('primarykey'));
            return response()->view('errors.blocked_mobile', ['role' => $userRole]);
        }

        return $next($request);
    }
}
