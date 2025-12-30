<?php

namespace App\Nova;

use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class LtiPlatform extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LtiPlatform>
     */
    public static $model = \App\Models\LtiPlatform::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
        'issuer',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required')
                ->default('Canvas Local Dev')
                ->help('A descriptive name for this LTI platform (e.g., "Canvas Production", "Canvas Local Dev")'),

            Text::make('Issuer')
                ->sortable()
                ->rules('required')
                ->default('https://canvas.instructure.com')
                ->help('The platform issuer URL. For Canvas: https://canvas.instructure.com'),

            Text::make('Auth Login URL')
                ->rules('required', 'url')
                ->default('https://canvas.docker/api/lti/authorize_redirect')
                ->help('Canvas: https://[host]/api/lti/authorize_redirect'),

            Text::make('Auth Token URL')
                ->rules('required', 'url')
                ->default('https://canvas.docker/login/oauth2/token')
                ->help('Canvas: https://[host]/login/oauth2/token'),

            Text::make('Key Set URL')
                ->rules('required', 'url')
                ->default('https://canvas.docker/api/lti/security/jwks')
                ->help('Canvas: https://[host]/api/lti/security/jwks'),

            Text::make('Deployments Count', function () {
                return $this->deployments->count();
            })->onlyOnIndex(),

            HasMany::make('Deployments', 'deployments', LtiDeployment::class),
            HasMany::make('Resource Links', 'resourceLinks', LtiResourceLink::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
