<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class LtiResourceLinkEntry extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LtiResourceLinkEntry>
     */
    public static $model = \App\Models\LtiResourceLinkEntry::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'lti_user_id',
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

            BelongsTo::make('User', 'user', User::class)
                ->sortable()
                ->rules('required')
                ->help('The SmartyCards user for this entry'),

            BelongsTo::make('Resource Link', 'resourceLink', LtiResourceLink::class)
                ->sortable()
                ->rules('required')
                ->help('The LTI resource link (Canvas assignment) for this entry'),

            Text::make('LTI User ID')
                ->sortable()
                ->help('Canvas user ID (sub claim from LTI launch)')
                ->readonly(),

            Code::make('Roles')
                ->json()
                ->hideFromIndex()
                ->help('LTI roles from launch (e.g., Learner, Instructor)')
                ->readonly(),

            Boolean::make('Is Staff')
                ->sortable()
                ->help('Whether this user has a staff role in Canvas')
                ->readonly(),

            DateTime::make('Last Launch At')
                ->sortable()
                ->help('When this user last launched this assignment from Canvas')
                ->readonly(),

            Number::make('Score')
                ->sortable()
                ->nullable()
                ->step(0.01)
                ->min(0)
                ->help('Score earned (NULL until assignment is completed)')
                ->readonly(),

            Number::make('Score Maximum')
                ->sortable()
                ->step(0.01)
                ->min(0)
                ->help('Maximum possible score (typically 100.00)')
                ->readonly(),

            BelongsTo::make('Activity Event', 'activityEvent', ActivityEvent::class)
                ->sortable()
                ->nullable()
                ->help('The activity event that triggered score completion')
                ->readonly(),

            DateTime::make('Completed At')
                ->sortable()
                ->nullable()
                ->help('When the user completed the assignment and earned a score')
                ->readonly(),

            DateTime::make('Submitted At')
                ->sortable()
                ->nullable()
                ->help('When the score was submitted to Canvas')
                ->readonly(),

            Boolean::make('Submission Success')
                ->sortable()
                ->nullable()
                ->help('Whether the score was successfully submitted to Canvas')
                ->readonly(),

            Textarea::make('Submission Error')
                ->hideFromIndex()
                ->nullable()
                ->help('Error message if score submission to Canvas failed')
                ->readonly(),

            DateTime::make('Created At')
                ->hideFromIndex()
                ->readonly(),

            DateTime::make('Updated At')
                ->hideFromIndex()
                ->readonly(),
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
