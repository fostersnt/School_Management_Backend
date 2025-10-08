<?php

namespace App\Http\Middleware;

use App\Helpers\General;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyContentType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isJson()) {
            return General::badRequestResponse("Invalid Content-Type. Only JSON requests are accepted");
        }

        return $next($request);
    }
}
