<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class LtiResourceLink extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LtiResourceLink>
     */
    public static $model = \App\Models\LtiResourceLink::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title', 'resource_link_id', 'context_id', 'context_label', 'context_title',
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

            BelongsTo::make('Deployment', 'deployment', LtiDeployment::class)
                ->sortable()
                ->rules('required')
                ->help('The LTI deployment this resource link belongs to'),

            BelongsTo::make('Deck', 'deck', Deck::class)
                ->sortable()
                ->nullable()
                ->help('The SmartyCards deck associated with this LTI assignment (optional)'),

            Text::make('Resource Link ID')
                ->sortable()
                ->rules('required')
                ->help('Unique identifier for this resource link from the LTI platform')
                ->readonly(),

            Text::make('Title')
                ->sortable()
                ->help('Assignment or module item title from Canvas')
                ->readonly(),

            Textarea::make('Description')
                ->hideFromIndex()
                ->help('Assignment description from Canvas')
                ->readonly(),

            Text::make('Context ID')
                ->sortable()
                ->help('Canvas course ID')
                ->readonly(),

            Text::make('Context Label')
                ->sortable()
                ->help('Canvas course code (e.g., MLSP 5211)')
                ->readonly(),

            Text::make('Context Title')
                ->sortable()
                ->help('Canvas course name')
                ->readonly(),

            Text::make('Lineitem URL')
                ->hideFromIndex()
                ->help('AGS endpoint for this specific assignment gradebook column')
                ->readonly(),

            Text::make('Lineitems URL')
                ->hideFromIndex()
                ->help('AGS endpoint for all gradebook columns in this course')
                ->readonly(),

            Code::make('Settings')
                ->json()
                ->hideFromIndex()
                ->help('Additional LTI settings for this resource link'),

            Code::make('Custom Params')
                ->json()
                ->hideFromIndex()
                ->help('Custom parameters passed during LTI launch')
                ->readonly(),

            Code::make('AGS Scopes')
                ->json()
                ->hideFromIndex()
                ->help('Assignment and Grade Services scopes/permissions granted')
                ->readonly(),

            Text::make('Grade Submissions', function () {
                return $this->gradeSubmissions->count();
            })->onlyOnIndex(),

            HasMany::make('Grade Submissions', 'gradeSubmissions', LtiGradeSubmission::class),
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
