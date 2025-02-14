<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeckMembershipResource;
use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
            'role' => 'required|string|in:viewer,editor,owner',
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

    public function leave(Request $request, Deck $deck)
    {
        Gate::authorize('leave', $deck);

        $membership = $deck->memberships()->where('user_id', $request->user()->id)->first();

        if (! $membership) {
            return response()->json([
                'message' => 'User is not a member of this deck.',
            ], 404);
        }

        $membership->delete();

        return response()->noContent();
    }

    public function joinAsViewer(Request $request, Deck $deck)
    {
        // if user is already a member of the deck, return 409
        $currentUserId = $request->user()->id;
        $isMemberOfDeck = $deck->memberships()->where('user_id', $currentUserId)->exists();
        if ($isMemberOfDeck) {
            return response()->json([
                'message' => 'User is already a member of this deck.',
            ], 409); // conflict
        }

        Gate::authorize('joinAsViewer', $deck);

        $membership = $deck->memberships()->create([
            'user_id' => $request->user()->id,
            'role' => DeckMembership::ROLE_VIEWER,
        ]);

        return DeckMembershipResource::make($membership)
            ->response()
            ->setStatusCode(201);
    }

    public function shareLink(Deck $deck)
    {
        Gate::authorize('update', $deck);

        $validated = request()->validate([
            'permission' => 'required|string|in:view,edit',
            'redirect_url' => "nullable|string|regex:/^\/decks\/{$deck->id}/",
        ]);

        $permission = $validated['permission'];
        $redirectUrl = $validated['redirect_url'] ?? "/decks/{$deck->id}";

        return response()->json([
            'url' => $this->getShareUrlForPermission($deck, $permission, $redirectUrl),
        ]);
    }

    protected function getShareUrlForPermission(Deck $deck, string $permission, string $redirectUrl)
    {
        $token = $deck->getTokenForPermission($permission)->token;

        $role = match ($permission) {
            'view' => 'viewer',
            'edit' => 'editor',
        };

        $signedURL = URL::signedRoute(
            'decks.memberships.acceptInvite',
            [
                'deck' => $deck->id,
                'fromUserId' => Auth::user()->id,
                'role' => $role,
                'redirectTo' => $redirectUrl,
                'token' => $token,
            ],
            expiration: null,
        );

        return $signedURL;
    }

    public function regenerateShareLink(Request $request, Deck $deck)
    {
        // check that the user is authorized to rotate tokens
        Gate::authorize('update', $deck);

        $validated = request()->validate([
            'permission' => 'required|string|in:view,edit',
            'redirect_url' => "nullable|string|regex:/^\/decks\/{$deck->id}/",
        ]);

        $permission = $validated['permission'];
        $redirectUrl = $validated['redirect_url'] ?? "/decks/{$deck->id}";

        // rotate the token
        $deck->rotateTokenForPermission($permission);

        $shareUrl = $this->getShareUrlForPermission($deck, $permission, $redirectUrl);

        return response()->json(['url' => $shareUrl]);

    }
}
