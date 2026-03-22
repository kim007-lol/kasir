<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoMode
{
    /**
     * Handle an incoming request.
     * Block ALL mutating methods AND sensitive GET routes for demo users.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (isDemoUser()) {

            // FIX #7: Block export/download routes untuk demo user (GET tapi sensitif)
            $blockedPatterns = [
                'reports/export-pdf',
                'reports/export-excel',
                'users-pelanggan/template',
                'transaksi/download',
                'transaksi/thermal',
            ];

            $currentPath = $request->path();
            foreach ($blockedPatterns as $pattern) {
                if (str_contains($currentPath, $pattern)) {
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Demo mode: Download/export tidak diizinkan.'
                        ], 403);
                    }
                    return redirect()->back()->with('error', 'Mode Demo Aktif: Fitur export/download tidak tersedia di akun demo.');
                }
            }

            // Allow all other GET requests
            if ($request->isMethod('GET')) {
                return $next($request);
            }

            // Block mutating methods: POST, PUT, PATCH, DELETE
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restricted: Akun demo hanya bisa melihat, tidak bisa menambah, mengubah, atau menghapus data.'
                ], 403);
            }

            return redirect()->back()->with('error', 'Mode Demo Aktif: Anda hanya memiliki akses baca (Read-Only). Aksi modifikasi data tidak diizinkan.');
        }

        return $next($request);
    }
}
