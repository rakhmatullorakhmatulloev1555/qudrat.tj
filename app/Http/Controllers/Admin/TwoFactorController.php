<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Inertia\Inertia;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    protected function getGoogle2FA()
    {
        return app('pragmarx.google2fa');
    }

    /**
     * Страница настройки 2FA
     */
    public function setup()
    {
        $user   = Auth::user();
        $google = $this->getGoogle2FA();

        // Генерируем секрет если не был создан
        if (!$user->two_factor_secret) {
            $user->update(['two_factor_secret' => $google->generateSecretKey()]);
        }

        $qrCodeUrl = $google->getQRCodeUrl(
            config('app.name') . ' Admin',
            $user->email,
            $user->two_factor_secret
        );

        $recoveryCodes = [];
        if ($user->two_factor_recovery_codes) {
            try {
                $recoveryCodes = json_decode(Crypt::decryptString($user->two_factor_recovery_codes), true) ?? [];
            } catch (DecryptException) {
                // Legacy plaintext — treat as expired; user must re-enable 2FA to get new codes
                $recoveryCodes = [];
            }
        }

        return Inertia::render('Admin/TwoFactor/Setup', [
            'qr_url'  => $qrCodeUrl,
            'secret'  => $user->two_factor_secret,
            'enabled' => $user->two_factor_enabled,
            'codes'   => $recoveryCodes,
        ]);
    }

    /**
     * Включить 2FA — проверяем код и активируем
     */
    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string|digits:6']);

        $user   = Auth::user();
        $google = $this->getGoogle2FA();

        $valid = $google->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Неверный код. Проверьте время на устройстве и попробуйте снова.']);
        }

        // Генерируем 8 кодов восстановления
        $recoveryCodes = collect(range(1, 8))->map(fn() =>
            strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4))
        )->toArray();

        $user->update([
            'two_factor_enabled'        => true,
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
        ]);

        return redirect()->route('admin.2fa.setup')
            ->with('success', '✅ Двухфакторная аутентификация успешно включена!');
    }

    /**
     * Отключить 2FA
     */
    public function disable(Request $request)
    {
        $request->validate(['password' => 'required|current_password']);

        Auth::user()->update([
            'two_factor_enabled'        => false,
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
        ]);

        return redirect()->route('admin.2fa.setup')
            ->with('success', 'Двухфакторная аутентификация отключена.');
    }

    /**
     * Страница подтверждения кода при входе
     */
    public function challenge()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('admin.login');
        }

        return Inertia::render('Admin/TwoFactor/Challenge');
    }

    /**
     * Проверка кода при входе
     */
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $userId = session('2fa_user_id');
        if (!$userId) {
            return redirect()->route('admin.login');
        }

        $user = \App\Models\User::findOrFail($userId);

        // Проверяем TOTP код
        $google = $this->getGoogle2FA();
        $code   = str_replace('-', '', strtoupper($request->code));

        if ($google->verifyKey($user->two_factor_secret, $request->code)) {
            Auth::login($user);
            session()->forget('2fa_user_id');
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Проверяем коды восстановления
        if ($user->two_factor_recovery_codes) {
            try {
                $codes = json_decode(Crypt::decryptString($user->two_factor_recovery_codes), true) ?? [];
            } catch (DecryptException) {
                $codes = [];
            }

            // Нормализуем обе стороны одинаково (убираем дефис + верхний регистр),
            // т.к. коды хранятся как «ABCD-EFGH», а $code уже без дефиса.
            $normalize = fn($c) => str_replace('-', '', strtoupper($c));
            $key = array_search($code, array_map($normalize, $codes));

            if ($key !== false) {
                // Используем код восстановления (одноразовый)
                unset($codes[$key]);
                $user->update([
                    'two_factor_recovery_codes' => Crypt::encryptString(json_encode(array_values($codes))),
                ]);

                Auth::login($user);
                session()->forget('2fa_user_id');
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
        }

        return back()->withErrors(['code' => 'Неверный код. Попробуйте снова.']);
    }
}
