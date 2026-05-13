<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && !empty($user->google2fa_secret)) {
            // Check if the user has already passed 2FA for this session
            if (!$request->session()->get('2fa_verified')) {
                // Ignore the 2FA verify routes themselves to avoid loops
                if (!$request->is('2fa/*') && !$request->is('logout')) {
                    return redirect()->route('2fa.index');
                }
            }
        }

        return $next($request);
    }
}
