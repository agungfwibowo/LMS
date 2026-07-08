<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Blokir user yang belum disetujui admin dari halaman yang dilindungi.
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user && ! $user->isApproved()) {
            if ($request->expectsJson()) {
                abort(403, 'Akun Anda belum disetujui oleh admin.');
            }

            return redirect()->route('approval.pending');
        }

        return $next($request);
    }
}
