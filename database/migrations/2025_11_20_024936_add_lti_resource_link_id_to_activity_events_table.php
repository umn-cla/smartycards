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
        Schema::table('activity_events', function (Blueprint $table) {
            // Link activity events to LTI assignments when accessed via Canvas
            // Null when practicing outside of LTI context
            $table->foreignId('lti_resource_link_id')
                ->nullable()
                ->after('deck_id')
                ->constrained('lti_resource_links')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_events', function (Blueprint $table) {
            $table->dropForeign(['lti_resource_link_id']);
            $table->dropColumn('lti_resource_link_id');
        });
    }
};
