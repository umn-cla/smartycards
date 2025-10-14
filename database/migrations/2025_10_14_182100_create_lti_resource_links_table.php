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
        Schema::create('lti_resource_links', function (Blueprint $table) {
            $table->id();

            // FK to deployment
            $table->foreignId('lti_deployment_id')
                ->constrained('lti_deployments')
                ->cascadeOnDelete();

            // Canvas' id for this specific assignment/link
            $table->string('resource_link_id');

            // Metadata from Canvas
            $table->string('title')->nullable(); // Assignment title
            $table->text('description')->nullable(); // Assignment description

            // Context (course) info
            $table->string('context_id'); // Canvas' Course ID
            $table->string('context_label')->nullable(); //  Course code, e.g. "PSY-101"
            $table->string('context_title')->nullable(); // Course title, e.g. "Introduction to Psychology"

            // store custom params (if any) we get from Canvas Deep Linking
            $table->json('custom_params')->nullable()->comment('Custom parameters from Canvas Deep Linking');


            // which deck this assignment uses
            $table->foreignId('deck_id')
                ->nullable()
                ->constrained('decks')
                ->nullOnDelete();

            // settings
            $table->json('settings')->nullable();

            $table->timestamps();

            // each resource link is unique per deployment
            $table->unique(['lti_deployment_id', 'resource_link_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lti_resource_links');
    }
};
