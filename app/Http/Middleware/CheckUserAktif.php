<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAktif
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = session('userID') ?? $request->cookie('userID') ?? null;
        $role = session('userRole') ?? $request->cookie('userRole');
        $primary = session('primarykey') ?? $request->cookie('primarykey');
        //dd([$id, $role, $primary]);
        $useraktif = DB::table($role)->where($primary, $id)->value('status');

        if (is_null($id) || is_null($role) || is_null($primary)) {
            // Log error atau redirect ke halaman login
            Log::warning('Middleware: Missing user session/cookie data. Redirecting to login.');
            return redirect()->route('login-staff')->with('error', 'Sesi Anda telah berakhir atau tidak valid. Silakan login kembali.');
        }
        if ($useraktif != "aktif") {
            return abort(403, 'User tidak dalam keadaan aktif, silahkan hubungi admin');
        }
        return $next($request);
    }
}
