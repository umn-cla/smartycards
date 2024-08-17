<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    const VIEW_ADMIN_PAGES = 'VIEW_ADMIN_PAGES';
}
