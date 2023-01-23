<?php

use Axel\Otp\Methods\MailOtp;

return [
    'database'             => [
        'connection' => env('DB_CONNECTION', 'mysql'),
    ],
    'notification_methods' => [
        'mail' => MailOtp::class
    ],
    'storage'              => 'cache' // table, cache
];