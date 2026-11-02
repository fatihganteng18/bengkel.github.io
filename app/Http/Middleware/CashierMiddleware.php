<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CashierMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'cashier') {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
