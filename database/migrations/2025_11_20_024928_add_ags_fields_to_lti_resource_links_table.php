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
        Schema::table('lti_resource_links', function (Blueprint $table) {
            // Cache AGS (Assignment and Grade Services) endpoints from LTI launch
            // These URLs are provided by Canvas in the launch JWT and tell us where to submit grades
            $table->text('lineitem_url')->nullable()->after('custom_params')
                ->comment('Direct URL to this assignment\'s gradebook column');
            $table->text('lineitems_url')->nullable()->after('lineitem_url')
                ->comment('URL to all line items in this context');
            $table->json('ags_scopes')->nullable()->after('lineitems_url')
                ->comment('What AGS operations are allowed (lineitem, score, result.readonly)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lti_resource_links', function (Blueprint $table) {
            $table->dropColumn(['lineitem_url', 'lineitems_url', 'ags_scopes']);
        });
    }
};
