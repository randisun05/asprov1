<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTwoFactorIsEnabled
{
    /**
     * Roles that must have a confirmed two-factor setup before using the
     * rest of the admin panel.
     */
    private const REQUIRED_ROLES = ['administrator', 'sekretariat'];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (
            $user
            && in_array($user->role, self::REQUIRED_ROLES, true)
            && is_null($user->two_factor_confirmed_at)
            && !$request->routeIs('admin.security.two-factor')
            && !$request->routeIs('two-factor.*')
            && !$request->is('user/two-factor-*')
            && !$request->routeIs('logout')
        ) {
            return redirect()->route('admin.security.two-factor')
                ->with('warning', 'Role Anda mewajibkan aktivasi 2FA sebelum melanjutkan.');
        }

        return $next($request);
    }
}
