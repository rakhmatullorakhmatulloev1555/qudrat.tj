<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ClientAuthController extends Controller
{
    /* ─── Форма логина ─────────────────────────────────────────── */

    public function loginForm(): mixed
    {
        if (Auth::check()) {
            return $this->redirectAuthenticated(Auth::user());
        }

        return Inertia::render('ClientLogin');
    }

    /* ─── Вход ──────────────────────────────────────────────────── */

    public function login(Request $request): mixed
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                // Generic error — do not reveal that the account exists but is deactivated
                return back()->withErrors(['email' => 'Неверный email или пароль.']);
            }

            $request->session()->regenerate();
            return $this->redirectAuthenticated($user);
        }

        return back()->withErrors(['email' => 'Неверный email или пароль.']);
    }

    /* ─── Регистрация клиента ───────────────────────────────────── */

    public function register(Request $request): mixed
    {
        $validated = $request->validate([
            'interest'       => 'required|string|max:50',
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'required|email|max:255|unique:users,email',
            'password'       => ['required', Password::min(12)->mixedCase()->numbers()->symbols(), 'confirmed'],
            'rooms'          => 'nullable|string|max:20',
            'budget'         => 'nullable|string|max:100',
            'floor_pref'     => 'nullable|string|max:50',
            'finish'         => 'nullable|string|max:50',
            'timeline'       => 'nullable|string|max:100',
            'invest_amount'  => 'nullable|string|max:100',
            'invest_goal'    => 'nullable|string|max:100',
            'contact_method' => 'nullable|string|max:50',
            'comment'        => 'nullable|string|max:1000',
        ], [
            'password.regex' => 'Пароль должен содержать прописные, строчные буквы, цифры и спецсимволы (минимум 12 символов).',
        ]);

        // Создаём учётную запись клиента
        $user = User::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'client',
            'interest' => $validated['interest'],
            'is_active' => true,
        ]);

        // Сохраняем заявку в CRM (для менеджеров)
        $interestLabels = [
            'apartment'  => 'Покупка квартиры',
            'invest'     => 'Инвестиции в недвижимость',
            'commercial' => 'Коммерческая недвижимость',
            'mining'     => 'Партнёрство (горнодобыча)',
        ];

        $note = "Регистрация клиента (личный кабинет)\n";
        $note .= "Интерес: " . ($interestLabels[$validated['interest']] ?? $validated['interest']) . "\n";
        if (!empty($validated['rooms']))         $note .= "Комнат: {$validated['rooms']}\n";
        if (!empty($validated['budget']))        $note .= "Бюджет: {$validated['budget']}\n";
        if (!empty($validated['floor_pref']))    $note .= "Этаж: {$validated['floor_pref']}\n";
        if (!empty($validated['finish']))        $note .= "Отделка: {$validated['finish']}\n";
        if (!empty($validated['timeline']))      $note .= "Срок: {$validated['timeline']}\n";
        if (!empty($validated['invest_amount'])) $note .= "Сумма инвестиций: {$validated['invest_amount']}\n";
        if (!empty($validated['invest_goal']))   $note .= "Цель инвестиций: {$validated['invest_goal']}\n";
        if (!empty($validated['contact_method'])) $note .= "Способ связи: {$validated['contact_method']}\n";
        if (!empty($validated['comment']))       $note .= "Комментарий: {$validated['comment']}\n";

        Lead::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'],
            'source'   => 'website',
            'interest' => $validated['interest'],
            'status'   => 'new',
            'notes'    => $note,
        ]);

        // Авто-вход после регистрации
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('client.cabinet')->with('registered', true);
    }

    /* ─── Выход ─────────────────────────────────────────────────── */

    public function logout(Request $request): mixed
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /* ─── Личный кабинет ────────────────────────────────────────── */

    public function cabinet(Request $request): Response
    {
        $user = Auth::user();

        // Если вошёл как сотрудник — перенаправить в CRM
        if (in_array($user->role, ['admin', 'manager', 'viewer'])) {
            return Inertia::location(route('admin.dashboard'));
        }

        return Inertia::render('Cabinet/Index', [
            'client' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'phone'    => $user->phone,
                'interest' => $user->interest,
                'since'    => $user->created_at->format('d.m.Y'),
            ],
            'flash' => [
                'registered' => session('registered'),
            ],
        ]);
    }

    /* ─── Обновление профиля ────────────────────────────────────── */

    public function updateProfile(Request $request): mixed
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:30',
        ]);

        $user->update($validated);

        return back()->with('success', 'Профиль обновлён.');
    }

    /* ─── Хелпер: редирект по роли ─────────────────────────────── */

    private function redirectAuthenticated(User $user): mixed
    {
        if (in_array($user->role, ['admin', 'manager', 'viewer'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('client.cabinet');
    }
}
