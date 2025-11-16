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
        Schema::table('users', function (Blueprint $table) {
            // Store LTI user ID (the 'sub' claim from LTI launch)
            // This is unique per platform, so we'll index it but not make it globally unique
            // since users could potentially come from multiple LMS platforms
            $table->string('lti_user_id')->nullable()->index()->after('umndid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('lti_user_id');
        });
    }
};
