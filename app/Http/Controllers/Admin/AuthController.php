<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return Inertia::render('Admin/Login');
    }

    public function login(Request $request)
    {
        // При ВХОДЕ пароль не валидируем на сложность — только проверяем совпадение.
        // Политика сложности применяется при создании/смене пароля, а не при логине,
        // иначе валидные пароли со «спецсимволами вне набора» не пропускаются.
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                LoginHistory::record(['user_id' => $user->id, 'email' => $user->email, 'status' => 'blocked']);
                return back()->withErrors(['email' => 'Ваш аккаунт деактивирован.']);
            }

            if (!in_array($user->role, ['admin', 'manager', 'viewer'])) {
                Auth::logout();
                LoginHistory::record(['user_id' => $user->id, 'email' => $user->email, 'status' => 'no_access']);
                return back()->withErrors(['email' => 'Нет доступа к панели управления.']);
            }

            // Если 2FA включена — разлогиниваем и отправляем на challenge
            if ($user->two_factor_enabled) {
                session(['2fa_user_id' => $user->id]);
                Auth::logout();
                LoginHistory::record(['user_id' => $user->id, 'email' => $user->email, 'status' => '2fa_pending']);
                return redirect()->route('admin.2fa.challenge');
            }

            $request->session()->regenerate();
            LoginHistory::record(['user_id' => $user->id, 'email' => $user->email, 'status' => 'success']);
            return redirect()->intended(route('admin.dashboard'));
        }

        LoginHistory::record(['email' => $request->email, 'status' => 'failed']);
        return back()->withErrors(['email' => 'Неверный email или пароль.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
