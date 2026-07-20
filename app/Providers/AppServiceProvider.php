<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Принудительный HTTPS в production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Tailwind-совместимые пагинация views
        Paginator::useTailwind();

        // ═══ RBAC Gates ═══
        // admin  — полный доступ
        // manager — создание/редактирование, без удаления и настроек
        // viewer  — только чтение

        Gate::define('admin-only', fn(User $user) =>
            $user->role === 'admin'
        );

        Gate::define('manage', fn(User $user) =>
            in_array($user->role, ['admin', 'manager'])
        );

        Gate::define('view-crm', fn(User $user) =>
            in_array($user->role, ['admin', 'manager', 'viewer'])
        );

        // Удаление разрешено только admin
        Gate::define('delete-record', fn(User $user) =>
            $user->role === 'admin'
        );

        // Настройки сайта — только admin
        Gate::define('site-settings', fn(User $user) =>
            $user->role === 'admin'
        );
    }
}
