<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Eğer kullanıcı admin değilse kendi dashboard’una gönder
        if (!$user->isAdmin()) {
            return $this->redirectByRole($user);
        }

        return $next($request);
    }

    /**
     * Kullanıcının rolüne göre yönlendirme
     */
    private function redirectByRole($user)
    {
        return match (true) {
            $user->isCalisan()        => redirect()->route('company.calisan'),
            $user->isBirimAmiri()     => redirect()->route('company.birim-amiri'),
            $user->isAlanAmiri()      => redirect()->route('company.alan-amiri'),
            $user->isIsgUzmani()      => redirect()->route('company.isg-uzmani'),
            $user->isIsverenVekili()  => redirect()->route('company.isveren-vekili'),
            default                   => redirect()->route('company.calisan'),
        };
    }
}
