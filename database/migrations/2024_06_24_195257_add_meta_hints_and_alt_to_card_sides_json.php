<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function initMeta(array $side): array
    {
        $side['meta'] = $side['meta'] ?? [];
        $side['meta']['hints'] = $side['meta']['hints'] ?? [];
        $side['meta']['alt'] = $side['meta']['alt'] ?? '';

        return $side;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('cards')->select('id', 'front', 'back')->chunkById(100, function ($cards) {
            foreach ($cards as $card) {
                $front = json_decode($card->front, true);
                $back = json_decode($card->back, true);

                $front = $this->initMeta($front);
                $back = $this->initMeta($back);

                DB::table('cards')
                    ->where('id', $card->id)
                    ->update([
                        'front' => json_encode($front),
                        'back' => json_encode($back),
                    ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No actions needed for down migration
    }
};
