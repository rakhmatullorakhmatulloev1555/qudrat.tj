<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::with('roles')
            ->when($request->search, fn($q, $s) =>
                $q->where(fn($q) => $q
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                )
            )
            ->when($request->role, fn($q, $r) =>
                $q->whereHas('roles', fn($q) => $q->where('name', $r))
            )
            ->when($request->status, fn($q, $s) =>
                $q->where('is_active', $s === 'active')
            )
            ->latest();

        $users = $query->paginate(25)->withQueryString()
            ->through(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'phone'      => $u->phone,
                'role'       => $u->role,
                'roles'      => $u->roles->pluck('name'),
                'is_active'  => $u->is_active,
                'two_fa'     => (bool) $u->two_factor_enabled,
                'created_at' => $u->created_at->format('d.m.Y'),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users'       => $users,
            'roles'       => Role::orderBy('name')->pluck('name'),
            'departments' => Department::where('is_active', true)->orderBy('name')->get(['id','name']),
            'filters'     => $request->only(['search','role','status']),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('admin-only');

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()],
            'role'     => 'required|string|exists:roles,name',
            'is_active'=> 'boolean',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'is_active' => $data['is_active'] ?? true,
        ]);
        $user->assignRole($data['role']);

        AuditLog::record('created', 'users', "Created user {$user->email}", model: $user);

        return back()->with('success', 'Пользователь создан.');
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('admin-only');

        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => "required|email|unique:users,email,{$user->id}",
            'phone'     => 'nullable|string|max:20',
            'password'  => ['nullable', Password::min(12)->mixedCase()->numbers()->symbols()],
            'role'      => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $old = $user->only(['name','email','phone','role','is_active']);

        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'role'      => $data['role'],
            'is_active' => $data['is_active'] ?? $user->is_active,
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles([$data['role']]);

        AuditLog::record('updated', 'users', "Updated user {$user->email}", model: $user,
            oldValues: $old, newValues: $user->fresh()->only(['name','email','phone','role','is_active']));

        return back()->with('success', 'Пользователь обновлён.');
    }

    public function destroy(User $user)
    {
        Gate::authorize('admin-only');

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Нельзя удалить самого себя.']);
        }
        AuditLog::record('deleted', 'users', "Deleted user {$user->email}", model: $user);
        $user->delete();
        return back()->with('success', 'Пользователь удалён.');
    }

    public function toggleStatus(User $user)
    {
        Gate::authorize('admin-only');

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Нельзя деактивировать самого себя.']);
        }
        $user->update(['is_active' => !$user->is_active]);
        AuditLog::record('updated', 'users', "Toggled status for {$user->email}",
            model: $user, newValues: ['is_active' => $user->is_active]);
        return back()->with('success', $user->is_active ? 'Пользователь активирован.' : 'Пользователь деактивирован.');
    }
}
