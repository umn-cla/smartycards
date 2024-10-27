<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo as FieldsBelongsTo;
use Laravel\Nova\Fields\DateTime as FieldsDateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActivityEvent extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ActivityEvent>
     */
    public static $model = \App\Models\ActivityEvent::class;

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
            FieldsBelongsTo::make('User', 'user', User::class)->sortable(),
            FieldsBelongsTo::make('Deck', 'deck', Deck::class)->sortable(),
            FieldsBelongsTo::make('Activity Type', 'activityType', ActivityType::class)->sortable(),
            Number::make('XP', 'xp')->sortable(),
            FieldsDateTime::make('Created At', 'created_at')->sortable(),
            FieldsDateTime::make('Updated At', 'updated_at')->sortable(),
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
