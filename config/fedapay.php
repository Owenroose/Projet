<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FedaPay Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'intégration FedaPay
    |
    */

    'public_key' => env('FEDAPAY_PUBLIC_KEY', ''),
    'secret_key' => env('FEDAPAY_SECRET_KEY', ''),
    'environment' => env('FEDAPAY_ENVIRONMENT', 'sandbox'), // 'sandbox' ou 'live'

    /*
    |--------------------------------------------------------------------------
    | URLs de callback
    |--------------------------------------------------------------------------
    */
    'callback_url' => env('APP_URL') . '/orders/payment/callback',
    'success_url' => env('APP_URL') . '/orders/success',
    'failure_url' => env('APP_URL') . '/orders/failure',

    /*
    |--------------------------------------------------------------------------
    | Configuration par défaut
    |--------------------------------------------------------------------------
    */
    'currency' => 'XOF',
    'country' => 'BJ',
];
