<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('id')
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'users_count' => $r->users_count,
                'permissions' => $r->permissions->pluck('name'),
                'is_system'   => in_array($r->name, ['super_admin','admin','client']),
            ]);

        $permissions = Permission::orderBy('name')->pluck('name')
            ->groupBy(fn($p) => explode(' ', $p)[1] ?? 'other');

        return Inertia::render('Admin/Roles/Index', [
            'roles'       => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('admin-only');

        $data = $request->validate([
            'name'        => 'required|string|max:60|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return back()->with('success', "Роль «{$role->name}» создана.");
    }

    public function update(Request $request, Role $role)
    {
        Gate::authorize('admin-only');

        if (in_array($role->name, ['super_admin'])) {
            return back()->withErrors(['error' => 'Нельзя изменить роль super_admin.']);
        }

        $data = $request->validate(['permissions' => 'array']);
        $role->syncPermissions($data['permissions'] ?? []);

        return back()->with('success', "Права роли «{$role->name}» обновлены.");
    }

    public function destroy(Role $role)
    {
        Gate::authorize('admin-only');

        if (in_array($role->name, ['super_admin','admin','client'])) {
            return back()->withErrors(['error' => 'Системные роли нельзя удалить.']);
        }
        $role->delete();
        return back()->with('success', "Роль удалена.");
    }
}
