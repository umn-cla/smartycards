<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class LtiDeployment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LtiDeployment>
     */
    public static $model = \App\Models\LtiDeployment::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'deployment_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'deployment_id',
        'client_id',
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

            BelongsTo::make('Platform', 'platform', LtiPlatform::class)
                ->sortable()
                ->rules('required')
                ->help('Select the LTI platform this deployment belongs to'),

            Text::make('Client ID')
                ->sortable()
                ->rules('required')
                ->help('The Client ID from Canvas (found in Admin > Developer Keys after creating the LTI key)'),

            Text::make('Deployment ID')
                ->sortable()
                ->rules('required')
                ->help('The Deployment ID from Canvas (found in Admin > Settings > Apps > [App] > Deployment Id)'),

            Text::make('Resource Links Count', function () {
                return $this->resourceLinks->count();
            })->onlyOnIndex(),

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
