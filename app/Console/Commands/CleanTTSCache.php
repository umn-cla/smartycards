<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

class CleanTTSCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tts-clean {--days=7 : The number of days to keep cache files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old TTS cache files from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);
        $count = 0;

        foreach (Storage::files('tts') as $file) {
            $lastModified = Storage::lastModified($file);

            if ($lastModified < $cutoffDate->timestamp) {
                Storage::delete($file);
                $count++;
            }
        }

        $this->info("Deleted {$count} TTS cache files older than {$days} days ({$cutoffDate->toDateString()})");
    }
}
