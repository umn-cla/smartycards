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
        Schema::create('lti_assignment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lti_resource_link_id')->constrained()->onDelete('cascade');
            $table->string('lti_user_id')->comment('The Canvas user ID (sub claim) for grade submission');

            // Score information
            $table->decimal('score', 8, 2)->nullable()->comment('User score - NULL until completed');
            $table->decimal('score_maximum', 8, 2)->default(100.00);

            // Activity tracking
            $table->foreignId('activity_event_id')->nullable()->constrained()->onDelete('set null')
                ->comment('The activity event that earned this score');
            $table->timestamp('completed_at')->nullable()->comment('When user completed the assignment');

            // Canvas submission tracking
            $table->timestamp('submitted_at')->nullable()->comment('When grade was submitted to Canvas');
            $table->boolean('submission_success')->nullable()->comment('Whether Canvas accepted the grade');
            $table->text('submission_error')->nullable()->comment('Error message if submission failed');

            $table->timestamps();

            // Unique constraint: one score per user per assignment
            $table->unique(['user_id', 'lti_resource_link_id'], 'user_assignment_unique');

            // Index for finding ungraded assignments
            $table->index(['lti_resource_link_id', 'score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lti_assignment_scores');
    }
};
