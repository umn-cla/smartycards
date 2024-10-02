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
        Schema::create('deck_invite_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deck_id')->constrained()->onDelete('cascade');
            $table->string('permission');
            $table->string('token')->unique();
            $table->timestamps();

            $table->unique(['deck_id', 'permission']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deck_invite_tokens');
    }
};
