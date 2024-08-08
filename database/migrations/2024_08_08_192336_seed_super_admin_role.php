<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

class SeedSuperAdminRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(['name' => Role::SUPER_ADMIN]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $role = Role::where('name', Role::SUPER_ADMIN)->first();
        if ($role) {
            $role->delete();
        }
    }
}
