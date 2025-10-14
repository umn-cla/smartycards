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
        Schema::create('lti_platforms', function (Blueprint $table) {
            $table->id();

            // Platform identity (Canvas' unique id)
            $table->string('issuer')->unique(); // e.g. https://canvas.umn.edu
            $table->string('name')->nullable(); // e.g. "UMN Canvas - Prod"

            // Platform OAuth/OIDC endpoints
            $table->string('auth_login_url'); // where to send login initiations
            $table->string('auth_token_url'); // where to exchange auth code for access token
            $table->string('key_set_url'); // Canvas' JKWS endpoint

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lti_platforms');
    }
};
