<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminCookie = $request->cookie('admin_session');
        $sessionTokenHash = $request->session()->get('admin_session_token_hash');

        if (
            ! (bool) $request->session()->get('is_admin', false)
            || ! is_string($adminCookie)
            || ! is_string($sessionTokenHash)
            || ! hash_equals($sessionTokenHash, hash('sha256', $adminCookie))
        ) {
            $request->session()->forget(['is_admin', 'admin_session_token_hash']);

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
