<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeckMembershipResource;
use App\Ldap\LdapUser;
use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DeckMembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Deck $deck)
    {
        Gate::authorize('viewAny', [DeckMembership::class, $deck]);

        $memberships = $deck->memberships->load('user');

        return DeckMembershipResource::collection($memberships);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Deck $deck)
    {

        Gate::authorize('create', [DeckMembership::class, $deck]);

        $validated = $request->validate([
            'umndid' => 'required|string',
            'role' => 'required|string|in:viewer,editor',
        ]);

        $umndid = $validated['umndid'];

        // check if user exists
        $user = User::query()
            ->where('umndid', $umndid)
            ->first();

        // if there is no user with the given umndid,
        // then search LDAP for the user info and create one

        if (! $user) {
            $ldapUser = LdapUser::query()
                ->where('umndid', $umndid)
                ->first();

            if (! $ldapUser) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            // create a new user
            $user = User::create([
                'umndid' => $umndid,
                'name' => $ldapUser->getFirstAttribute('displayname'),
                'email' => $ldapUser->getFirstAttribute('umndisplaymail'),
                'first_name' => $ldapUser->getFirstAttribute('givenname'),
                'last_name' => $ldapUser->getFirstAttribute('sn'),
                'email' => $ldapUser->getFirstAttribute('mail'),
                'password' => bcrypt(Str::random(16)),
            ]);
        }

        if (! $user) {
            throw new \Exception('Error creating user.');
        }

        // check if user is already a member of the deck
        if ($deck->memberships()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is already a member of this deck.',
            ], 409); // conflict
        }

        $membership = DeckMembership::create([
            'deck_id' => $deck->id,
            'user_id' => $user->id,
            'role' => $validated['role'],
        ]);

        return DeckMembershipResource::make($membership)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DeckMembership $membership)
    {
        Gate::authorize('view', $membership);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeckMembership $membership)
    {
        Gate::authorize('update', $membership);

        $validated = $request->validate([
            'role' => 'required|string|in:viewer,editor',
        ]);

        $membership->update($validated);

        return DeckMembershipResource::make($membership);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeckMembership $membership)
    {
        Gate::authorize('delete', $membership);

        $membership->delete();

        return response()->noContent();
    }

    public function acceptInvite(Request $request, Deck $deck)
    {
        // add the user to the deck with the role of viewer
        $membership = DeckMembership::create([
            'deck_id' => $deck->id,
            'user_id' => $request->user()->id,
            'role' => 'viewer',
        ]);

        return DeckMembershipResource::make($membership);
    }

    public function share(Deck $deck)
    {
        Gate::authorize('update', $deck);

        $signedURL = URL::signedRoute('decks.memberships.acceptInvite', [
            'deck' => $deck->id,
            'user' => auth()->user()->id,
            'role' => 'viewer',
        ]);

        return response()->json(['url' => $signedURL]);
    }
}
