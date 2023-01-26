<?php

if (!function_exists('create_token')) {
    function create_token(): string
    {
        return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }


}

if (!function_exists('create_otp')) {
    function create_otp(): int
    {
        if (config('app.env') != 'production')
            return 123456;
        return mt_rand(100000, 999999);
    }
}

if (!function_exists('user_ip')) {
    function user_ip(): string
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
            $ipaddress .= '' . $_SERVER['HTTP_CF_CONNECTING_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress .= '' . $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress .= '' . $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress .= '' . $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress .= '' . $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress .= '' . $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress .= '' . $_SERVER['REMOTE_ADDR'];

        if ($ipaddress == "")
            $ipaddress = 'UNKNOWN';
        if ($ipaddress == "::1")
            $ipaddress = '0.0.0.0';

        return $ipaddress;
    }
}