<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Kullanıcı giriş yapmış mı?
        if (!$user) {
            return redirect()->route('login');
        }

        // Kullanıcının bir şirketi var mı?
        if (!$user->company_id && !session()->has('current_company_id')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Bir şirkete ait olmanız gerekiyor.');
        }

        return $next($request);
    }
}
