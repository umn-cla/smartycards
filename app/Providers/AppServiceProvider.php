<?php

namespace App\Providers;

use App\Models\Role;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiServiceConnector;
use App\Services\Lti\LtiDatabase;
use App\Services\Lti\LtiCache;
use App\Services\Lti\LtiCookie;
use GuzzleHttp\Client;

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

        // Bind LTI interface implementations
        $this->app->singleton(IDatabase::class, LtiDatabase::class);
        $this->app->singleton(ICache::class, LtiCache::class);
        $this->app->singleton(ICookie::class, LtiCookie::class);

        // Bind service connector for LTI services (AGS, NRPS, Deep Linking)
        $this->app->singleton(ILtiServiceConnector::class, function ($app) {
            $cache = $app->make(ICache::class);
            $httpClient = new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);

            return (new LtiServiceConnector($cache, $httpClient))
                ->setDebuggingMode(config('app.debug'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions
        // like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole(Role::SUPER_ADMIN) ? true : null;
        });

        // a leeway to account for clock drift (in seconds)
        // used in JWT validation with Packback LTI library
        JWT::$leeway = 5;
    }
}
