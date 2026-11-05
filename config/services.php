<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'google_pay' => [
        'environment' => env('GOOGLE_PAY_ENVIRONMENT', 'TEST'),
        'merchant_id' => env('GOOGLE_PAY_MERCHANT_ID', ''),
        'merchant_name' => env('GOOGLE_PAY_MERCHANT_NAME', 'AkuSehat - Neura Cakrawira Solusi'),
    ],

    'midtrans' => [
        'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'),
        'server_key' => env('MIDTRANS_SERVER_KEY', ''),
        'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
        'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
    ],

    'google_play' => [
        'package_name' => env('GOOGLE_PLAY_PACKAGE_NAME', 'neuracakrawira.asia.akusehat'),
        'service_account_json' => env('GOOGLE_PLAY_SERVICE_ACCOUNT_JSON'),
        'require_device_integrity' => env('GOOGLE_PLAY_REQUIRE_DEVICE_INTEGRITY', false),
    ],

    'turnstile' => [
        'enabled' => env('TURNSTILE_ENABLED', true),
        'site_key' => env('TURNSTILE_SITE_KEY') ?: env('CLOUDFLARE_TURNSTILE_SITE_KEY') ?: env('CF_TURNSTILE_SITE_KEY', '1x00000000000000000000AA'),
        'secret_key' => env('TURNSTILE_SECRET_KEY') ?: env('CLOUDFLARE_TURNSTILE_SECRET_KEY') ?: env('CF_TURNSTILE_SECRET_KEY', '1x0000000000000000000000000000000AA'),
    ],

    's2s' => [
        'strict_mode' => (bool) env('S2S_STRICT_MODE', false),
        'secret' => env('S2S_SHARED_SECRET'),
        'tolerance_seconds' => (int) env('S2S_TOLERANCE_SECONDS', 60),
    ],

    'vault' => [
        'secret_key' => env('DATA_SECRET_KEY'),
    ],

    'fcm' => [
        'project_id' => env('FCM_PROJECT_ID', ''),
        'server_key' => env('FCM_SERVER_KEY', ''),
        'service_account_json' => env('FCM_SERVICE_ACCOUNT_JSON', ''),
    ],

];
