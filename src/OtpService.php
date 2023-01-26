<?php

namespace Axel\Otp;

use Axel\Otp\Actions\CacheAction;
use Axel\Otp\Actions\TableAction;
use Axel\Otp\Exceptions\OtpServiceException;
use Illuminate\Support\Facades\Cache;

class OtpService
{
    /**
     * @throws OtpServiceException
     */
    public static function create(string $key, string $method, string $to, array $data = []): array
    {
        $expires = self::action()->getTokenLifetime();

        $data = [
            'key'                 => $key,
            'notification_method' => $method,
            'notification_to'     => $to,
            'ip_address'          => user_ip(),
            'verify_token'        => create_token(),
            'verify_code'         => create_otp(),
            'expires_at'          => $expires->toDateTimeString(),
            'data'                => $data,
            'attempts'            => 0,
            'verified'            => false
        ];

        self::action()->save($data['verify_token'], $data);
        return $data;
    }

    public static function get(string $token)
    {
        return self::action()->get($token);
    }

    public static function getData(string $token)
    {
        $otp = self::get($token);
        return $otp ? $otp['data'] : null;
    }

    /**
     * @throws OtpServiceException
     */
    public static function check(string $token, string $code): string
    {
        return self::action()->check($token, $code);
    }

    public static function delete(string $token)
    {
        self::action()->delete($token);
    }

    private static function action()
    {
        $storage = config('otp.storage') ?: 'cache';
        return $storage === 'cache'
            ? new CacheAction()
            : new TableAction();
    }
}