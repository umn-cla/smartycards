<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowLtiConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:lti-config {--url=: App\'s public url (e.g. https://smartycards.cla.umn.edu)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the LTI configuration for Canvas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = $this->option('url') ?: config('app.url');

        if (!$baseUrl) {
            $this->error('Base URL is not set. Please provide it using the --url option or set APP_URL in your .env file.');
            return 1;
        }
        $this->info('=== LTI 1.3 Configuration ===');
        $this->newLine();
        $this->line("Redirect URIs: {$baseUrl}/lti/launch");
        $this->line("Target Link URI: {$baseUrl}/lti/launch");
        $this->line("OpenID Connect Initiation URL: {$baseUrl}/lti/login");
        $this->line("Public JWK URL: {$baseUrl}/lti/jwks");

        return 0;
    }
}
