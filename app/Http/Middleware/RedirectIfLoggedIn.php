<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfLoggedIn
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

        if ($userID && $userRole) {
            switch ($userRole) {
                case 'siswa':
                    return redirect()->route('info.presensi')->with('success', 'Berhasil Login sebagai siswa');
                case 'wali_kelas':
                    return redirect()->route('dashboard-wali-kelas')->with('success', 'Berhasil Login sebagai wali kelas');
                case 'guru_mapel':
                    return redirect()->route('nilai.fetch')->with('success', 'Berhasil Login sebagai guru mapel');
                case 'staff':
                    return redirect()->route('data.fetch')->with('success', 'Berhasil Login sebagai Staff');
                default:
                    // Jika role tidak dikenal, arahkan ke landing
                    return redirect()->route('landing')->with('warning', 'Session sudah habis, harap login kembali');
            }
        }

        return $next($request);
    }
}
