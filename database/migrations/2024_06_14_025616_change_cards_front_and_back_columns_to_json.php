<?php

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
            DB::table('cards')->select('id', 'front', 'back')->chunkById(100, function ($cards) {
                foreach ($cards as $card) {
                    DB::table('cards')
                        ->where('id', $card->id)
                        ->update([
                            'front' => json_encode(['type' => 'text', 'content' => $card->front]),
                            'back' => json_encode(['type' => 'text', 'content' => $card->back]),
                        ]);
                }
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
