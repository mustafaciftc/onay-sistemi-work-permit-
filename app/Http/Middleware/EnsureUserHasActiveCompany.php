<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;   // <<< BUNU EKLE

class EnsureUserHasActiveCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->getActiveCompany()) {
                if (!$request->routeIs('companies.create')) {
                    return redirect()->route('companies.create')
                        ->with('warning', 'Devam etmek için bir şirket oluşturmalısınız.');
                }
            }
        }

        return $next($request);
    }
}
