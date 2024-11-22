<?php

namespace App\Providers;

use App\Library\FeatureFlagService;
use App\Models\Role;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // register telescope only in local environment
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        // register the feature flag service
        $this->app->singleton(FeatureFlagService::class, function () {
            return new FeatureFlagService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions
        // like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole(Role::SUPER_ADMIN) ? true : null;
        });

    }
}
