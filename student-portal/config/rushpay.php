<?php

return [
    'base_url' => env('RUSHPAY_BASE_URL', 'https://api.rushpay.cash/v1'),
    'api_key' => env('RUSHPAY_API_KEY'),
    'client_key' => env('RUSHPAY_CLIENT_KEY', env('RUSHPAY_CLIENT_ID')),
    'client_secret' => env('RUSHPAY_CLIENT_SECRET'),
    'merchant_id' => env('RUSHPAY_MERCHANT_ID'),
    'callback_url' => env('RUSHPAY_CALLBACK_URL', env('APP_URL') . '/student/payments/rushpay/callback'),
];
