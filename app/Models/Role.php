<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    const SUPER_ADMIN = 'SUPER_ADMIN';
}
