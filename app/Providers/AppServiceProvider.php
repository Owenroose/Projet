<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use FedaPay\FedaPay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configuration FedaPay avec vérification
        if (class_exists('FedaPay\FedaPay')) {
            $apiKey = env('FEDAPAY_SECRET_KEY');
            $environment = env('FEDAPAY_ENV', 'sandbox');

            if ($apiKey) {
                FedaPay::setApiKey($apiKey);
                FedaPay::setEnvironment($environment);
            }
        }
    }
}
