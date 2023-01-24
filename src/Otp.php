<?php

namespace Axel\Otp;

use Axel\Otp\Exceptions\InvalidDataException;
use Axel\Otp\Exceptions\InvalidMethodParametersException;
use Axel\Otp\Exceptions\InvalidNotificationMethod;
use Axel\Otp\Exceptions\InvalidRecipientException;

class Otp
{
    private $Method;
    private $To;
    private $Data = [];
    private $Methods;

    public function __construct()
    {
        $this->Methods = config('otp.notification_methods');
    }

    /**
     * @throws InvalidNotificationMethod
     */
    public function method(string $method): Otp
    {
        if (!array_key_exists($method, $this->Methods)) {
            throw new InvalidNotificationMethod("Notification method $method is not found");
        }

        $this->Method = $method;
        return $this;
    }

    /**
     * @throws InvalidMethodParametersException
     * @throws InvalidRecipientException
     */
    public function to($to): Otp
    {
        if (is_null($this->Method)) {
            throw new InvalidMethodParametersException("Notification method does not found");
        }

        if (is_null($to)) {
            throw new InvalidRecipientException('Recipient is not found');
        }

        $this->To = $to;
        return $this;
    }

    /**
     * @throws InvalidNotificationMethod
     * @throws InvalidDataException
     */
    public function data(array $data): Otp
    {
        if (is_null($this->Method)) {
            throw new InvalidNotificationMethod("Notification method does not set");
        }

        if (!array_keys($data)) {
            throw new InvalidDataException('Notification data is empty');
        }

        $this->Data = $data;
        return $this;
    }

    /**
     * @throws InvalidMethodParametersException
     * @throws InvalidNotificationMethod
     */
    public function send(): void
    {
        if (is_null($this->Method)) {
            throw new InvalidNotificationMethod("Notification method does not set");
        }

        if (is_null($this->To) || !array_keys($this->Data)) {
            throw new InvalidMethodParametersException('Some parameters does not set');
        }

        $app = $this->Methods[$this->Method];
        app($app)->to($this->To)->data($this->Data)->send();
    }
}