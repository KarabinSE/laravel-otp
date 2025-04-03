<?php

namespace Ichtrojan\Otp;

use Illuminate\Support\ServiceProvider;
use Ichtrojan\Otp\Commands\OtpInstallCommand;
use Ichtrojan\Otp\Commands\CleanOtps;


class OtpServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/otp.php',
            'otp'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            // Register commands
            $this->commands([
                OtpInstallCommand::class,
                CleanOtps::class,
            ]);

            // Publish config
            $this->publishes([
                __DIR__ . '/../config/otp.php' => config_path('otp.php'),
            ], 'otp-config');

            // Publish migration
            if (!class_exists('CreateOtpsTable')) {
                $timestamp = date('Y_m_d_His');
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_otps_table.php.stub' =>
                        database_path("migrations/{$timestamp}_create_otps_table.php"),
                ], 'otp-migrations');
            }
        }
    }
}
