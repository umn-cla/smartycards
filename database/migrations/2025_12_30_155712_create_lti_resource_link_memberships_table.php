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
        Schema::create('lti_resource_link_memberships', function (Blueprint $table) {
            $table->id();

            // User who has this role in the Canvas course
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Canvas course/assignment this membership applies to
            $table->foreignId('lti_resource_link_id')
                ->constrained('lti_resource_links')
                ->cascadeOnDelete();

            // LTI roles array from launch (e.g., ["http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor"])
            $table->json('roles')->nullable();

            // Computed flag for quick filtering - true if user has instructor/TA/admin role
            $table->boolean('is_staff')->default(false)->index();

            // Track when user last launched this resource link
            $table->timestamp('last_launch_at')->nullable();

            $table->timestamps();

            // Each user can only have one membership per resource link
            $table->unique(['user_id', 'lti_resource_link_id'], 'user_resource_link_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lti_resource_link_memberships');
    }
};
