<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Определяем среду ПЕРЕД использованием в CSP
        $host = $request->getHost();
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1', '::1'])
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.local');

        // CSP — защита от XSS атак
        // В dev-режиме разрешаем Vite dev-сервер и Google Fonts
        $isDev = app()->environment('local', 'development') || $isLocalhost;
        $vite  = $isDev ? ' http://127.0.0.1:5173 http://127.0.0.1:5174 http://[::1]:5173' : '';
        $fonts = "https://fonts.googleapis.com https://fonts.gstatic.com";

        $csp = "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net{$vite}; "
            . "style-src 'self' 'unsafe-inline' {$fonts}{$vite}; "
            . "img-src 'self' data: https: blob:; "
            . "font-src 'self' data: {$fonts}; "
            . "connect-src 'self' https:{$vite}" . ($isDev ? ' ws://127.0.0.1:5173 ws://127.0.0.1:5174 ws://[::1]:5173' : '') . "; "
            . "frame-ancestors 'none'; "
            . "base-uri 'self'; "
            . "form-action 'self'";
        $response->headers->set('Content-Security-Policy', $csp);

        if (app()->environment('production') && !$isLocalhost) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
