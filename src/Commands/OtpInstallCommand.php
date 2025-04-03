<?php

namespace Ichtrojan\Otp\Commands;

use Illuminate\Console\Command;

class OtpInstallCommand extends Command
{
    protected $signature = 'otp:install';
    protected $description = 'Publish the OTP config and migration file';

    public function handle()
    {
        $this->info('Publishing OTP config file...');

        if ($this->call('vendor:publish', ['--tag' => 'otp-config']) === 0) {
            $this->info('✅ Config file published successfully.');
        } else {
            $this->error('❌ Failed to publish config file.');
        }

        $this->info('Publishing OTP migration file...');

        if ($this->call('vendor:publish', ['--tag' => 'otp-migrations']) === 0) {
            $this->info('✅ Migration file published successfully.');
        } else {
            $this->error('❌ Failed to publish migration file.');
        }

        $this->newLine();

        $this->comment('You can now run: php artisan migrate');

        return 0;
    }
}