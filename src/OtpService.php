<?php

namespace Axel\Otp;

use Axel\Otp\Actions\CacheAction;
use Axel\Otp\Actions\TableAction;
use Axel\Otp\Enum\Status;
use Axel\Otp\Exceptions\OtpServiceException;

class OtpService
{
    private static $action;

    public function __construct()
    {
        $storage = config('otp.storage') ?: 'cache';
        self::$action = $storage === 'cache'
            ? new CacheAction()
            : new TableAction();
    }

    /**
     * @throws OtpServiceException
     */
    public static function create(string $key, string $method, string $to, array $data = []): array
    {
        $expires = self::$action->getTokenLifetime();

        $data = [
            'key'                 => $key,
            'notification_method' => $method,
            'notification_to'     => $to,
            'ip_address'          => user_ip(),
            'verify_token'        => create_token(),
            'verify_code'         => create_otp(),
            'expires_at'          => $expires,
            'data'                => $data,
            'attempts'            => 0,
            'verified'            => false
        ];

        self::$action->save($data['verify_token'], $data);
        return $data;
    }

    public static function get(string $token)
    {
        return self::$action->get($token);
    }

    /**
     * @throws OtpServiceException
     */
    public static function check(string $token, string $code): string
    {
        return self::$action->check($token, $code);
    }
}