<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateLtiKeys extends Command
{
    protected $signature = 'app:generate-lti-keys';

    protected $description = 'Generate RSA keypair for LTI 1.3 integration';

    public function handle(): int
    {
        $this->info('Generating LTI RSA keypair (4096 bits)...');
        $this->newLine();

        // Generate keys in memory
        $privateKey = $this->generatePrivateKey();
        $publicKey = $this->generatePublicKey($privateKey);

        // Base64 encode for .env storage to avoid newline/spacing issues
        $privateKeyEncoded = base64_encode($privateKey);
        $publicKeyEncoded = base64_encode($publicKey);
        $kid = 'smartycards-' . date('Y-m');

        // Display keys for manual copy/paste
        $this->displayKeys($privateKeyEncoded, $publicKeyEncoded, $kid);

        return 0;
    }

    /**
     * Generate private key using OpenSSL
     */
    private function generatePrivateKey(): string
    {
        $config = [
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $privateKeyResource = openssl_pkey_new($config);

        if (!$privateKeyResource) {
            $this->error('Failed to generate private key: ' . openssl_error_string());
            exit(1);
        }

        openssl_pkey_export($privateKeyResource, $privateKey);

        return $privateKey;
    }

    /**
     * Generate public key from private key
     */
    private function generatePublicKey(string $privateKey): string
    {
        $privateKeyResource = openssl_pkey_get_private($privateKey);

        if (!$privateKeyResource) {
            $this->error('Failed to read private key: ' . openssl_error_string());
            exit(1);
        }

        $publicKeyDetails = openssl_pkey_get_details($privateKeyResource);

        return $publicKeyDetails['key'];
    }

    /**
     * Display keys for manual addition to .env
     */
    private function displayKeys(string $privateKey, string $publicKey, string $kid): void
    {
        $this->info('✅ Keys generated successfully!');
        $this->newLine();
        $this->line('Copy the following to your .env file:');
        $this->newLine();
        $this->line('# LTI 1.3 Configuration');
        $this->line('# Keys are base64 encoded to avoid newline/spacing issues');
        $this->line("LTI_PRIVATE_KEY={$privateKey}");
        $this->line("LTI_PUBLIC_KEY={$publicKey}");
        $this->line("LTI_KID={$kid}");
        $this->newLine();
        $this->warn('⚠️  Keep your private key secure and never commit it to version control');
    }
}
