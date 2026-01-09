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
        Schema::create('lti_resource_link_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lti_resource_link_id')->constrained()->onDelete('cascade');
            $table->string('lti_user_id')->comment('Canvas user ID (sub claim)');

            // Role/Access (from memberships)
            $table->json('roles')->comment('LTI roles from launch');
            $table->boolean('is_staff')->index();
            $table->timestamp('last_launch_at')->nullable();

            // Score/Performance (from scores)
            $table->decimal('score', 8, 2)->nullable()->comment('NULL until completed');
            $table->decimal('score_maximum', 8, 2)->default(100.00);
            $table->foreignId('activity_event_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('completed_at')->nullable();

            // Canvas Submission Status
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('submission_success')->nullable();
            $table->text('submission_error')->nullable();

            $table->timestamps();

            // Constraints & Indexes
            $table->unique(['user_id', 'lti_resource_link_id']);
            $table->index(['lti_resource_link_id', 'score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lti_resource_link_entries');
    }
};
