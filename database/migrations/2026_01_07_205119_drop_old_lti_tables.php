<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only drop if they exist
        Schema::dropIfExists('lti_assignment_scores');
        Schema::dropIfExists('lti_resource_link_memberships');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore dropped tables in down migration
        // This would require recreating the entire table structure
        // which is better handled by keeping the old migration files
    }
};
