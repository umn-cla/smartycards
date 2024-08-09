<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use URL;

class Card extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Card>
     */
    public static $model = \App\Models\Card::class;

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

    public function renderCardBlock($block)
    {
        $type = $block['type'];
        $content = $block['content'];
        $isValidUrl = filter_var($content, FILTER_VALIDATE_URL);

        if ($type === 'image' && $isValidUrl) {
            return Text::make($type, fn () => "<img src='{$content}' style='max-width: 100px; max-height:100px; display: block;' />")->asHtml();
        }

        if ($type === 'audio' && $isValidUrl) {
            return Text::make($type, fn () => "<audio controls source src='{$content}'></audio>")->asHtml();
        }

        if ($isValidUrl) {
            return URL::make($type, fn () => $content);
        }

        if ($type === 'text') {
            return Text::make($type, fn () => $content)->asHtml();
        }

        return Text::make($type, fn () => $content);
    }

    public function makeCardSide(string $label, $blocks)
    {
        return Stack::make(
            $label,
            collect($blocks)->map(fn ($block) => $this->renderCardBlock($block))->toArray()
        );
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            $this->makeCardSide('Front', $this->front)->exceptOnForms(),
            $this->makeCardSide('Back', $this->back)->exceptOnForms(),

            Code::make('front')->json()->onlyOnForms(),
            Code::make('back')->json()->onlyOnForms(),
            BelongsTo::make('Deck'),
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
