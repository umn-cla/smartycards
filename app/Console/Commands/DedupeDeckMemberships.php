<?php

namespace App\Console\Commands;

use App\Models\DeckMembership;
use Illuminate\Console\Command;

class DedupeDeckMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dedupe-deck-memberships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes duplicate deck memberships, keeping the membership with the highest privileged role.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roles = [
            DeckMembership::ROLE_VIEWER => 1,
            DeckMembership::ROLE_EDITOR => 2,
            DeckMembership::ROLE_OWNER => 3,
        ];

        // fetch all memberships
        $memberships = DeckMembership::all();

        // group by deck_id and user_id
        $groupedMemberships = $memberships->groupBy(['deck_id', 'user_id']);
    }
}
