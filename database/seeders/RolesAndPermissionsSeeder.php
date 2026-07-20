<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── PERMISSIONS ──────────────────────────────────────────
        $permissions = [
            // Dashboard
            'view dashboard',

            // Users
            'view users', 'create users', 'edit users', 'delete users',
            'manage roles',

            // CRM
            'view leads', 'create leads', 'edit leads', 'delete leads', 'export leads',
            'view contacts', 'create contacts', 'edit contacts', 'delete contacts',
            'view deals', 'create deals', 'edit deals', 'delete deals',

            // Projects & Apartments
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view apartments', 'create apartments', 'edit apartments', 'delete apartments',
            'manage reservations',

            // Documents
            'view documents', 'create documents', 'edit documents', 'delete documents',
            'approve documents',

            // CMS
            'view cms', 'edit cms', 'publish cms',
            'manage translations',
            'manage seo',
            'manage media',

            // Mining
            'view mining', 'create mining', 'edit mining', 'delete mining',

            // News & Content
            'view news', 'create news', 'edit news', 'delete news',
            'manage partners',
            'manage testimonials',
            'manage gallery',

            // Analytics
            'view analytics', 'export analytics',

            // Settings & Security
            'view settings', 'edit settings',
            'view audit logs',
            'view security',
            'manage integrations',

            // Communication
            'send notifications',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── ROLES ─────────────────────────────────────────────────

        // Super Admin — все права (через wildcard gate)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // Admin — все кроме управления ролями super_admin
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::where('name', '!=', 'manage roles')->get());

        // Manager — операционный менеджер
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view dashboard',
            'view leads','create leads','edit leads','export leads',
            'view contacts','create contacts','edit contacts',
            'view deals','create deals','edit deals',
            'view projects','view apartments','manage reservations',
            'view documents','create documents',
            'view analytics',
            'send notifications',
        ]);

        // Sales — отдел продаж
        $sales = Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
        $sales->syncPermissions([
            'view dashboard',
            'view leads','create leads','edit leads',
            'view contacts','create contacts','edit contacts',
            'view deals','create deals','edit deals',
            'view apartments','manage reservations',
            'view documents',
        ]);

        // Editor — редактор контента
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions([
            'view dashboard',
            'view cms','edit cms',
            'manage translations',
            'manage seo',
            'manage media',
            'view news','create news','edit news',
            'manage partners','manage testimonials','manage gallery',
        ]);

        // Viewer — только просмотр
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'view dashboard',
            'view leads','view contacts','view deals',
            'view projects','view apartments',
            'view documents',
            'view analytics',
        ]);

        // Client — клиентский портал (отдельный guard не нужен, просто роль)
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        // ── ASSIGN ROLES TO EXISTING USERS ──────────────────────
        User::where('role', 'admin')->orWhere('role', 'super_admin')
            ->get()->each(fn($u) => $u->syncRoles(['super_admin']));
        User::where('role', 'manager')
            ->get()->each(fn($u) => $u->syncRoles(['manager']));
        User::where('role', 'viewer')
            ->get()->each(fn($u) => $u->syncRoles(['viewer']));
        User::where('role', 'client')
            ->get()->each(fn($u) => $u->syncRoles(['client']));

        $this->command->info('✅ Roles and permissions seeded successfully.');
    }
}
