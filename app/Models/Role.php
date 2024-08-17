<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    const SUPER_ADMIN = 'SUPER_ADMIN';

    public function givePermissionsByName(array $permissionNames)
    {
        Permission::whereIn('name', $permissionNames)
            ->get()
            ->each(fn ($p) => $this->givePermissionTo($p));

        return $this;
    }
}
