<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'Ваш аккаунт деактивирован.']);
        }

        if (!in_array($user->role, ['admin', 'manager', 'viewer'])) {
            abort(403, 'Нет доступа.');
        }

        return $next($request);
    }
}
