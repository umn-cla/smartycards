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
        Schema::table('lti_resource_link_memberships', function (Blueprint $table) {
            $table->string('lti_user_id')->nullable()->comment('The sub claim from LTI launch, stored for deferred grade submissions');
            $table->index('lti_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lti_resource_link_memberships', function (Blueprint $table) {
            $table->dropIndex(['lti_user_id']);
            $table->dropColumn('lti_user_id');
        });
    }
};
