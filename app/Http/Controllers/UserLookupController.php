<?php

namespace App\Http\Controllers;

use App\Ldap\LdapUser;
use Illuminate\Http\Request;

class UserLookupController extends Controller
{
    public function __invoke(Request $req)
    {
        $validated = $req->validate([
            'q' => 'required|string',
        ]);

        $searchValue = $validated['q'];

        $users = LdapUser::query()
            ->whereStartsWith('uid', $searchValue)
            ->orWhereContains('displayname', $searchValue)
            ->select(['uid', 'displayname', 'umndisplaymail', 'umndid'])
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'internet_id' => $user->uid[0],
                    'display_name' => $user->displayname[0],
                    'email' => $user->umndisplaymail[0] ?? null,
                    'umndid' => $user->umndid[0] ?? null,
                ];
            });

        return response()->json(['data' => $users]);

    }
}
