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
        Schema::create('lti_deployments', function (Blueprint $table) {
            $table->id();

            // Link to platform
            $table->foreignId('lti_platform_id')
                ->constrained('lti_platforms')
                ->cascadeOnDelete();

            // Canvas gives us these when we register our tool
            $table->string('deployment_id');
            $table->string('client_id');

            $table->timestamps();

            $table->unique(['lti_platform_id', 'deployment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lti_deployments');
    }
};
