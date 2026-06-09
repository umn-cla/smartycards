<?php

namespace App\Nova;

use App\Models\DeckMembership as DeckMembershipModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class DeckMembership extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\DeckMembership>
     */
    public static $model = \App\Models\DeckMembership::class;

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['user', 'deck'];

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public function title()
    {
        // user/deck can be null when the related record is soft-deleted;
        // fall back to the foreign key so the row stays identifiable.
        $user = $this->user?->name ?? "User #{$this->user_id}";
        $deck = $this->deck?->name ?? "Deck #{$this->deck_id}";

        return "{$user} ({$this->role}) - {$deck}";
    }

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
            BelongsTo::make('User'),
            BelongsTo::make('Deck'),
            // Text::make('Role')->sortable(),
            Select::make('Role')->options([
                DeckMembershipModel::ROLE_OWNER => 'Owner',
                DeckMembershipModel::ROLE_EDITOR => 'Editor',
                DeckMembershipModel::ROLE_VIEWER => 'Viewer',
            ])->displayUsingLabels()->sortable(),
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
