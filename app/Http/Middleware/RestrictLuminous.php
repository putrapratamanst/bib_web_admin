<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// User Luminous (PT berbeda) hanya boleh membuka modul Luminous; sisanya milik BIB.
class RestrictLuminous
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role === 'luminous' && ! $request->routeIs('luminous.*', 'logout')) {
            return $request->expectsJson()
                ? abort(403)
                : redirect()->route('luminous.invoices.index');
        }

        return $next($request);
    }
}
