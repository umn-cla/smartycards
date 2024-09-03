<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Deck extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Deck>
     */
    public static $model = \App\Models\Deck::class;

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
        'name', 'description',
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
            Text::make('name')->sortable(),
            Text::make('description')->sortable(),
            Boolean::make('is_public')->sortable(),

            // card count
            Text::make('Card Count', function () {
                return $this->cards->count();
            })->onlyOnIndex(),

            // membership count
            Text::make('Membership Count', function () {
                return $this->memberships->count();
            })->onlyOnIndex(),

            HasMany::make('Cards'),

            HasMany::make('Memberships', 'memberships', DeckMembership::class),
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
