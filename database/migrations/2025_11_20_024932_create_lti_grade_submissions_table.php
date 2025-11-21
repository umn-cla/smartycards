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
        Schema::create('lti_grade_submissions', function (Blueprint $table) {
            $table->id();

            // What was graded - the Canvas assignment
            $table->foreignId('lti_resource_link_id')
                ->constrained('lti_resource_links')
                ->cascadeOnDelete();

            // Who was graded
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // What triggered this submission (optional - for traceability)
            $table->foreignId('activity_event_id')
                ->nullable()
                ->constrained('activity_events')
                ->nullOnDelete();

            // Score details
            $table->decimal('score_given', 8, 2)->comment('e.g., 85.50');
            $table->decimal('score_maximum', 8, 2)->default(100.00);
            $table->string('activity_progress')->default('Completed')
                ->comment('Initialized, Started, InProgress, Submitted, Completed');
            $table->string('grading_progress')->default('FullyGraded')
                ->comment('NotReady, Failed, Pending, PendingManual, FullyGraded');

            // Submission metadata
            $table->string('lti_user_id')->comment('The sub claim from LTI launch');
            $table->string('launch_id')->nullable()->comment('Launch ID used for this submission');
            $table->timestamp('submitted_at');

            // Response tracking
            $table->boolean('success')->default(false);
            $table->text('error_message')->nullable();
            $table->json('request_payload')->nullable()->comment('What we sent to Canvas');
            $table->json('response_data')->nullable()->comment('What Canvas returned');

            // For retry logic
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index(['user_id', 'lti_resource_link_id']);
            $table->index(['success', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lti_grade_submissions');
    }
};
