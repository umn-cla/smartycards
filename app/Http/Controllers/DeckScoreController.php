<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\LtiResourceLinkEntry;
use Gate;

class DeckScoreController extends Controller
{
    public function index(Deck $deck)
    {
        Gate::authorize('view', $deck);

        $user = request()->user();

        // Determine if user is staff in any resource link for this deck
        $isStaff = LtiResourceLinkEntry::query()
            ->whereHas('resourceLink', fn ($q) => $q->where('deck_id', $deck->id))
            ->where('user_id', $user->id)
            ->where('is_staff', true)
            ->exists();

        // Students: See only their own entries
        // Staff: See all student entries for resource links where they have staff role
        $query = LtiResourceLinkEntry::query()
            ->whereHas('resourceLink', fn ($q) => $q->where('deck_id', $deck->id))
            ->with(['resourceLink', 'user']);

        if ($isStaff) {
            // Get resource link IDs where user is staff
            $staffResourceLinkIds = LtiResourceLinkEntry::query()
                ->whereHas('resourceLink', fn ($q) => $q->where('deck_id', $deck->id))
                ->where('user_id', $user->id)
                ->where('is_staff', true)
                ->pluck('lti_resource_link_id');

            // Show all student entries for those resource links
            $query->where('is_staff', false)
                ->whereIn('lti_resource_link_id', $staffResourceLinkIds);
        } else {
            // Show only this user's entries
            $query->where('user_id', $user->id);
        }

        $entries = $query->get()->map(function ($entry) use ($isStaff) {
            return [
                'id' => $entry->id,
                'resource_link' => [
                    'id' => $entry->resourceLink->id,
                    'title' => $entry->resourceLink->title,
                    'context_title' => $entry->resourceLink->context_title,
                    'context_label' => $entry->resourceLink->context_label,
                    'canvas_url' => $entry->resourceLink->getCanvasUrl(),
                ],
                'is_staff' => $entry->is_staff,
                'user' => $isStaff ? [
                    'id' => $entry->user->id,
                    'name' => $entry->user->name,
                    'email' => $entry->user->email,
                ] : null,
                'score' => $entry->isCompleted() ? [
                    'score' => $entry->score,
                    'score_maximum' => $entry->score_maximum,
                    'score_percentage' => $entry->score_maximum != 0 ? ($entry->score / $entry->score_maximum) * 100 : null,
                    'completed_at' => $entry->completed_at,
                    'submitted_at' => $entry->submitted_at,
                    'submission_success' => $entry->submission_success,
                    'submission_error' => $entry->submission_error,
                ] : null,
            ];
        });

        return response()->json([
            'user_role' => $isStaff ? 'staff' : 'student',
            'entries' => $entries,
        ]);
    }
}
