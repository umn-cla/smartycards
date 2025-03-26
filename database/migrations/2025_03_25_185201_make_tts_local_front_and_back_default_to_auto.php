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
        DB::table('decks')->whereNull('tts_locale_front')->update(['tts_locale_front' => 'auto']);
        DB::table('decks')->whereNull('tts_locale_back')->update(['tts_locale_back' => 'auto']);

        Schema::table('decks', function (Blueprint $table) {
            $table->string('tts_locale_front')->default('auto')->change();
            $table->string('tts_locale_back')->default('auto')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('decks', function (Blueprint $table) {
            $table->string('tts_locale_front')->nullable()->dropDefault()->change();
            $table->string('tts_locale_back')->nullable()->dropDefault()->change();
        });
    }
};
