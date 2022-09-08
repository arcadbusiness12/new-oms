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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'getgive' => [
        'apiKey' => env('GET_GIVE_KEY', ''),
        'url' => env('GET_GIVE_URL', '')
    ],
    'maraxpress' => [
        'apiKey' => env('MARA_API_KEY', ''),
        'url' => env('MARA_API_URL', '')
    ],
    'tfmExpress' => [
        'url' => env('TFM_API_URL', ''),
        'accountNumber' => env('TFM_ACCOUNT_NUMBER', ''),
        'userName' => env('TFM_USER_NAME', ''),
        'password' => env('TFM_PASSWORD', ''),
    ],
    'vivaExpress' => [
        'url' => env('VIVAEXPRESS_API_URL', ''),
        'apiKey' => env('VIVAEXPRESS_API_KEY', '')
    ],
    'shamilExpress' => [
        'url' => env('SHAMIL_API_URL', ''),
        'accountNumber' => env('SHAMIL_ACCOUNT_NUMBER', '')
    ],
    'leopardsExpress' => [
        'url' => env('LEOPARDS_API_URL', ''),
        'accountNumber' => env('LEOPARDS_ACCOUNT_NUMBER', '')
    ],
    'fetchrExpress' => [
        'url' => env('FETCHR_API_URL', ''),
        'apiToken' => env('FETCHR_API_TOKEN', ''),
        'accountNumber' => env('FETCHR_ACCOUNT_NUMBER', ''),
        'client_address_id' => env('FETCHR_CLIENT_ADDRESS_ID', '')
    ],
    'tcsCourier' => [
        'accountNumber' => env('TCS_ACCOUNT_NUMBER', ''),
        'url' => env('TCS_API_URL', ''),
        'apiKey' => env('TCS_API_KEY', ''),
        'username' => env('TCS_USERNAME', ''),
        'password' => env('TCS_PASSWORD', '')
    ],
    'jeebly' => [
        'url' => env('JEEBLY_API_URL', ''),
        'apiKey' => env('JEEBLY_API_KEY', ''),
        'clientCode' => env('JEEBLY_CLIENT_CODE', '')
    ],
    'risingStar' => [
        'url' => env('RISINGSTAR_API_URL', ''),
        'apiKey' => env('RISINGSTAR_API_KEY', ''),
        'clientCode' => env('RISINGSTAR_CLIENT_CODE', '')
    ],
    'deliveryPanda' => [
      'url' => env('DELIVERPANDA_API_URL', ''),
      'apiKey' => env('DELIVERPANDA_API_KEY', ''),
      'clientCode' => env('DELIVERPANDA_CLIENT_CODE', '')
    ],
    'nexCourier' => [
      'url' => env('NEXCOURIER_API_URL', ''),
      'apiKey' => env('NEXCOURIER_API_KEY', ''),
      'clientCode' => env('NEXCOURIER_CLIENT_CODE', '')
    ],
    'jtexpress' => [
      'url' => env('JTExpress_API_URL', ''),
      'apiKey' => env('JTExpress_apiAccount', ''),
      'clientCode' => env('JTExpress_CLIENT_CODE', ''),
      'password' => env('JTExpress_Password', ''),
      'privateKey' => env('JTExpress_privateKey', '')
    ]
];
