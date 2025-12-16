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
        Schema::table('lti_grade_submissions', function (Blueprint $table) {
            // The user_id foreign key is currently using the composite index
            // We need to create a standalone index first before we can drop the composite
            $table->index('user_id', 'idx_user_id');

            // Now we can drop the old composite index
            $table->dropIndex(['user_id', 'lti_resource_link_id']);

            // Add optimized composite index for latestPerUser queries
            // This supports:
            // - GROUP BY user_id, lti_resource_link_id
            // - MAX(submitted_at) aggregation
            // - JOIN conditions on all three fields
            // - Queries filtering by user_id alone (leftmost prefix)
            $table->index(['user_id', 'lti_resource_link_id', 'submitted_at'], 'idx_user_resource_submitted');

            // Add index on lti_resource_link_id for efficient whereIn queries and ordering
            $table->index(['lti_resource_link_id', 'submitted_at'], 'idx_resource_submitted');

            // Remove the standalone user_id index since our composite index covers it
            $table->dropIndex('idx_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lti_grade_submissions', function (Blueprint $table) {
            // Create temporary index for the FK
            $table->index('user_id', 'idx_user_id');

            // Drop the optimized indices
            $table->dropIndex('idx_user_resource_submitted');
            $table->dropIndex('idx_resource_submitted');

            // Restore the old composite index
            $table->index(['user_id', 'lti_resource_link_id']);

            // Drop the temporary index
            $table->dropIndex('idx_user_id');
        });
    }
};
