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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [

        'client_id' => '131383197768-jebj321h3t1gk6cvrsofoo47fd64qmdf.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'https://www.fiveferns.in/auth/google/callback',
    ],

    'facebook' => [
        'client_id'     => '328528275284431',
        'client_secret' => '1a0108ccb20cbf3281f4a8cc017cff89',
        'redirect'      => 'https://www.fiveferns.in/auth/facebook/callback',
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URI')
    ]

];
