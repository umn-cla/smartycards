<?php

use App\Models\DeckMembership;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove duplicate deck memberships, keeping the
     * membership with the highest privileged role
     * this is a prereq for adding a unique constraint
     */
    public function dedupeMemberships()
    {
        $roles = [
            DeckMembership::ROLE_VIEWER => 1,
            DeckMembership::ROLE_EDITOR => 2,
            DeckMembership::ROLE_OWNER => 3,
        ];

        DB::table('deck_memberships')
            ->select('deck_id', 'user_id')
            ->groupBy('deck_id', 'user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->each(function ($membership) use ($roles) {
                $duplicates = DeckMembership::where('deck_id', $membership->deck_id)
                    ->where('user_id', $membership->user_id)
                    ->get();

                $duplicates->sortBy(function ($membership) use ($roles) {
                    return $roles[$membership->role];
                });

                $duplicates->slice(1)->each(function ($membership) {
                    $membership->delete();
                });
            });
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->dedupeMemberships();

        Schema::table('deck_memberships', function (Blueprint $table) {
            $table->unique(['deck_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deck_memberships', function (Blueprint $table) {
            $table->dropUnique(['deck_id', 'user_id']);
        });
    }
};
