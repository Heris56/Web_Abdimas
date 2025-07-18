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
     * @param  string|null  $expectedRole
     */
    public function handle(Request $request, Closure $next, string $expectedRole = null): Response
    {
        $userID = $request->cookie('userID');
        $userRole = $request->cookie('userRole');

        if (!$userID || !$userRole) {
            return redirect()->route('landing')->with('warning', 'Silakan login terlebih dahulu');
        }

        if ($expectedRole && $userRole !== $expectedRole) {
            // Beri pesan error dan arahkan ke halaman yang sesuai (misal: dashboard atau landing)
            switch ($userRole) {
                case 'siswa':
                    return redirect()->route('landing')->with('error', 'Anda tidak memiliki akses ke halaman selain Siswa. Silahkan lakukan login');
                case 'wali_kelas':
                    return redirect()->route('landing')->with('error', 'Anda tidak memiliki akses ke halaman selain WaliKelas. Silahkan lakukan login');
                case 'guru_mapel':
                    return redirect()->route('landing')->with('error', 'Anda tidak memiliki akses ke halaman selain GuruMapel. Silahkan lakukan login');
                case 'staff':
                    return redirect()->route('landing')->with('error', 'Anda tidak memiliki akses ke halaman selain Staff. Silahkan lakukan login');
                default:
                    // Jika role tidak dikenal, arahkan ke landing
                    return redirect()->route('landing')->with('error', 'Akses ditolak.');
            }
        }

        return $next($request);
    }
}
