<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Admin Gates
        Gate::define('admin:episodes.manage', function ($user) {
            return $user->isAdmin() && (
                $user->hasPermission('episodes.create') ||
                $user->hasPermission('episodes.edit') ||
                $user->hasPermission('episodes.delete')
            );
        });

        Gate::define('admin:astuces.moderate', function ($user) {
            return $user->isAdmin() && $user->hasPermission('astuces.moderate');
        });

        Gate::define('admin:partenariats.manage', function ($user) {
            return $user->isAdmin() && $user->hasPermission('partenariats.manage');
        });

        Gate::define('admin:newsletter.manage', function ($user) {
            return $user->isAdmin() && $user->hasPermission('newsletter.manage');
        });

        Gate::define('admin:users.manage', function ($user) {
            return $user->isAdmin() && $user->hasPermission('users.manage');
        });

        Gate::define('admin:logs.view', function ($user) {
            return $user->isAdmin() && $user->hasPermission('logs.view');
        });

        Gate::define('admin:settings.manage', function ($user) {
            return $user->isAdmin() && $user->hasPermission('settings.manage');
        });

        Gate::define('admin:blog.manage', function ($user) {
            return $user->isAdmin() && (
                $user->hasPermission('blog.create') ||
                $user->hasPermission('blog.edit') ||
                $user->hasPermission('blog.delete')
            );
        });
    }
}
