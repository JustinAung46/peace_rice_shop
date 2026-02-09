<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('view-inventory', function (User $user) {
            return $user->hasPermission('inventory_access');
        });

        Gate::define('view-pos', function (User $user) {
            return $user->hasPermission('pos_access');
        });

        Gate::define('view-profit', function (User $user) {
            return $user->hasPermission('profit_access');
        });
    }
}

