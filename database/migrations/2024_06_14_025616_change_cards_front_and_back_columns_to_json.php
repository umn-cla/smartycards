<?php

use App\Models\Card;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            // for each text column, convert it into json stringified object
            Card::all()->each(function ($card) {
                $card->front = json_encode([
                    'type' => 'text',
                    'content' => $card->front,
                ]);
                $card->back = json_encode([
                    'type' => 'text',
                    'content' => $card->back,
                ]);
                $card->save();
            });

            // change the column type to json
            $table->json('front')->change();
            $table->json('back')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            // convert the column back to text
            $table->text('front')->change();
            $table->text('back')->change();
        });
    }
};
