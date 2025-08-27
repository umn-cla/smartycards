<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLTI13Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create LTI13 issuers table
        Schema::create('lti13_issuers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("host");
            $table->string("client_id");
            $table->string("auth_login_url");
            $table->string("auth_token_url");
            $table->string("key_set_url");
            $table->text("private_key");
            $table->string("kid");
        });

        // Create LTI13 deployments table
        Schema::create('lti13_deployments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("deployment_id");
            $table->biginteger("issuer_id")->unsigned();
            $table->foreign("issuer_id")->references("id")->on("lti13_issuers")->onDelete("cascade");
        });

        // Create LTI13 resource links table
        Schema::create('lti13_resource_links', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("resource_link");
            $table->biginteger("deployment_id")->unsigned()->nullable();
            $table->foreign("deployment_id")->references("id")->on("lti13_deployments")->onDelete("set null");
            $table->json("endpoint");
        });

        // Add LTI13 sub_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string("lti13_sub_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove LTI13 sub_id from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn("lti13_sub_id");
        });

        // Drop LTI13 tables in reverse order
        Schema::dropIfExists('lti13_resource_links');
        Schema::dropIfExists('lti13_deployments');
        Schema::dropIfExists('lti13_issuers');
    }
}
