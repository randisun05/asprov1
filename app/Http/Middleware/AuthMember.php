<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class AuthMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
       $member = auth()->guard('member')->user();

        if (!$member) {

            // 🔥 SIMPAN URL TUJUAN
            session(['url.intended' => $request->fullUrl()]);

            Session::flash('error', 'Silakan login terlebih dahulu.');

            return redirect('/user/login');
        }

        // Membership can expire while a session is still active (login
        // itself is only checked once, at sign-in) - catch that here too so
        // an expired member is force-logged-out on their very next request
        // instead of keeping access until they happen to log in again.
        if ($member->isExpired()) {
            auth()->guard('member')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Session::flash('error', 'Masa berlaku keanggotaan Anda telah berakhir pada ' . $member->expires_at->translatedFormat('d F Y') . '. Silakan hubungi admin untuk perpanjangan.');

            return redirect('/user/login');
        }

        return $next($request);
    }
}
