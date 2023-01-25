<?php

use Axel\Otp\Methods\MailOtp;

return [
    /*
     * OTP Storage: table or cash
     */
    'storage'              => 'cache', // table, cache

    /*
     * Database connection
     */
    'database'             => [
        'connection' => env('DB_CONNECTION', 'mysql'),
    ],

    /*
     * Notification methods
     */
    'notification_methods' => [
        'mail' => MailOtp::class
    ],

    /*
     * Token lifetime: in minutes
     */
    'token_lifetime'       => 15,

    /*
     * Attempts: amount
     */
    'attempts'             => 5,

    /*
     * Time in blacklist: in minutes
     */
    'lock_lifetime'        => 15,

    /*
     * Resending time: in minutes
     */
    'resending_time'       => 5,

    /*
     * Checking IP address
     */
    'ip_checking'          => true
];