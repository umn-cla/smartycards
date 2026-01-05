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
            $table->dropColumn(['retry_count', 'last_retry_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lti_grade_submissions', function (Blueprint $table) {
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();
        });
    }
};
