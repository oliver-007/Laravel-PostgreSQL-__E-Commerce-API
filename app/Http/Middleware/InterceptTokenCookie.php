<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InterceptTokenCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // Use request()->cookie() to pull the automatically decrypted value
        $cookieToken = request()->cookie('access_token');

        if ($cookieToken && !$request->headers->has('Authorization')) {
            // URL decode to turn %7C back into a literal | character
            $rawToken = urldecode($cookieToken);

            $request->headers->set('Authorization', 'Bearer ' . $rawToken);
        }

        return $next($request);
    }
}