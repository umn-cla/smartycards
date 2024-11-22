<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'umndid' => $user->umndid,
            'emplid' => $user->emplid,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'is_admin' => $user->hasRole(Role::SUPER_ADMIN),
            'capabilities' => [
                'canViewAdminPages' => $user->can(Permission::VIEW_ADMIN_PAGES),
            ],
            'updated_at' => $user->updated_at,
            'created_at' => $user->created_at,
        ]);
    }
}
