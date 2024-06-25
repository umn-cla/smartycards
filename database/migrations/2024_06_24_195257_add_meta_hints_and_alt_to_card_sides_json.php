<?php

use App\Models\Card;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private function initMeta(array $side): array
    {
        $side['meta'] ??= [];
        $side['meta']['hints'] ??= [];
        $side['meta']['alt'] ??= '';

        return $side;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Card::all()->each(function (Card $card) {
            $card->front = $this->initMeta($card->front);
            $card->back = $this->initMeta($card->back);
            $card->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
