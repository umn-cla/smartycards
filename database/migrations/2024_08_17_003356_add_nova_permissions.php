<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Permission::create([
            'name' => Permission::VIEW_ADMIN_PAGES,
            'label' => Permission::VIEW_ADMIN_PAGES,
        ]);

        Role::firstWhere('name', Role::SUPER_ADMIN)->givePermissionsByName([
            Permission::VIEW_ADMIN_PAGES,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', Permission::VIEW_ADMIN_PAGES)->delete();
    }
};
