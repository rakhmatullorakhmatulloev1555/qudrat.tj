<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    /* ── 1. Форма запроса ссылки ─────────────────────────────── */

    public function forgotForm(): Response
    {
        return Inertia::render('ForgotPassword');
    }

    /* ── 2. Отправка письма со ссылкой ───────────────────────── */

    public function sendResetLink(Request $request): mixed
    {
        $request->validate(['email' => 'required|email|max:255']);

        // Always return success to prevent user enumeration
        Password::sendResetLink($request->only('email'));

        return back()->with('status', __('passwords.sent'));
    }

    /* ── 3. Форма сброса пароля ──────────────────────────────── */

    public function resetForm(Request $request, string $token): Response
    {
        return Inertia::render('ResetPassword', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /* ── 4. Сохранение нового пароля ─────────────────────────── */

    public function reset(Request $request): mixed
    {
        $request->validate([
            'token'                 => 'required|string',
            'email'                 => 'required|email|max:255',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('client.login')->with('status', __('passwords.reset'));
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
