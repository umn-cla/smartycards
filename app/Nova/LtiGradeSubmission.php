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

class LtiGradeSubmission extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LtiGradeSubmission>
     */
    public static $model = \App\Models\LtiGradeSubmission::class;

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
        'id',
        'lti_user_id',
        'launch_id',
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

            BelongsTo::make('Resource Link', 'resourceLink', LtiResourceLink::class)
                ->sortable()
                ->rules('required')
                ->help('The Canvas assignment this grade was submitted to')
                ->readonly(),

            BelongsTo::make('User', 'user', User::class)
                ->sortable()
                ->nullable()
                ->help('The SmartyCards user who received this grade')
                ->readonly(),

            BelongsTo::make('Activity Event', 'activityEvent', ActivityEvent::class)
                ->sortable()
                ->nullable()
                ->help('The activity event that triggered this grade submission')
                ->readonly(),

            Number::make('Score Given')
                ->step(0.01)
                ->sortable()
                ->help('The numeric score submitted to Canvas')
                ->readonly(),

            Number::make('Score Maximum')
                ->step(0.01)
                ->sortable()
                ->help('The maximum possible score')
                ->readonly(),

            Text::make('Score %', function () {
                return number_format($this->getScorePercentage(), 2) . '%';
            })->onlyOnIndex(),

            Text::make('Activity Progress')
                ->sortable()
                ->help('LTI activity progress status (Initialized, Started, InProgress, Submitted, Completed)')
                ->readonly(),

            Text::make('Grading Progress')
                ->sortable()
                ->help('LTI grading progress status (NotReady, Failed, Pending, PendingManual, FullyGraded)')
                ->readonly(),

            Text::make('LTI User ID')
                ->hideFromIndex()
                ->help('The LTI user ID from Canvas (may differ from SmartyCards user ID)')
                ->readonly(),

            Text::make('Launch ID')
                ->hideFromIndex()
                ->help('The LTI launch ID associated with this submission')
                ->readonly(),

            DateTime::make('Submitted At')
                ->sortable()
                ->help('When the grade was submitted to Canvas')
                ->readonly(),

            Boolean::make('Success')
                ->sortable()
                ->help('Whether the grade submission was successful')
                ->readonly(),

            Textarea::make('Error Message')
                ->hideFromIndex()
                ->nullable()
                ->help('Error message if the submission failed')
                ->readonly(),

            Code::make('Request Payload')
                ->json()
                ->hideFromIndex()
                ->help('The full AGS request payload sent to Canvas')
                ->readonly(),

            Code::make('Response Data')
                ->json()
                ->hideFromIndex()
                ->help('The response data received from Canvas')
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
