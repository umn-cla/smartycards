<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use App\Nova\Dashboards\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Fortify;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::userTimezone(function (Request $request) {
            return $request->user()?->timezone ?? 'America/Chicago';
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes(['auth'])
            ->register();
    }

    /**
     * Configure the Nova authorization services.
     * This overrides the default authorization method in
     * NovaApplicationServiceProvider so that the gate applies
     * in local environments as well.
     *
     * @return void
     */
    protected function authorization()
    {
        $this->gate();

        Nova::auth(function ($request) {
            return Gate::check('viewNova', [$request->user()]);
        });
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function (User $user) {
            return $user->can(Permission::VIEW_ADMIN_PAGES);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Registers Nova's booted callback that bootstraps the /admin page
        // routes. Without this, only the nova-api/* routes load and /admin
        // falls through to the SPA fallback.
        parent::register();

        // Work around a deployment issue during `artisan:route:cache`:
        // "Unable to prepare route [logout] for serialization. Another
        // route has already been assigned name [logout]."
        // Disable Fortify's routes to avoid the conflict.
        Fortify::ignoreRoutes();
    }
}
