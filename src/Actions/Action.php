<?php

namespace Axel\Otp\Actions;

use Axel\Otp\Enum\Status;
use Axel\Otp\Enum\Update;
use Axel\Otp\Exceptions\OtpServiceException;
use Illuminate\Support\Carbon;

abstract class Action
{
    /**
     * @throws OtpServiceException
     */
    public function check(string $key, string $code): string
    {
        $data = $this->get($key);
        $this->changeIP($key);

        if (empty($data)) return Status::EXPIRED;
        if ($this->isBlocked($data['notification_to'])) return Status::BLOCKED;

        if ($data['verify_code'] != $code) {
            if (($data['attempts'] + 1) == $this->getAttempts()) {
                $this->block($data['notification_to']);
                $this->resetAttempts($key);
            } else {
                $this->addAttempt($key);
            }

            return Status::WRONG_CODE;
        }

        $this->verify($key);
        return Status::SUCCESS;
    }

    /*
     * Condition functions
     */

    public function ipChecking(): bool
    {
        try {
            return $this->getConfig("ip_checking", "IP Checking");
        } catch (OtpServiceException $e) {
            return false;
        }
    }

    /*
     * Functions for getting data
     */

    /**
     * @throws OtpServiceException
     */
    public function getResendingTime(): Carbon
    {
        return $this->getLifetime("resending_time", 'Resending time');
    }

    /**
     * @throws OtpServiceException
     */
    public function getAttempts()
    {
        return $this->getConfig('attempts', 'Attempts');
    }

    /**
     * @throws OtpServiceException
     */
    public function getLockLifetime(): Carbon
    {
        return $this->getLifetime("lock_lifetime", "Lock lifetime");
    }

    /**
     * @throws OtpServiceException
     */
    public function getTokenLifetime(): Carbon
    {
        return $this->getLifetime("token_lifetime", "Token lifetime");
    }

    /**
     * @throws OtpServiceException
     */
    private function getLifetime(string $key, string $title): Carbon
    {
        $lifetime = $this->getConfig($key, $title);
        return now()->addMinutes($lifetime);
    }

    /**
     * @throws OtpServiceException
     */
    private function getConfig(string $key, string $title)
    {
        $data = config("otp.$key");
        if (is_null($data)) {
            throw new OtpServiceException("$title does not set");
        }

        return $data;
    }

    /*
     * Action functions
     */

    public function verify(string $key)
    {
        $this->updateData($key, [
            'verified' => true,
            'attempts' => 0
        ]);
    }

    public function changeIP(string $key)
    {
        $this->updateData($key, [
            'ip_address' => user_ip()
        ]);
    }

    public function addAttempt(string $key)
    {
        $this->updateData($key, [
            'attempts' => Update::INCREMENT
        ]);
    }

    public function resetAttempts(string $key)
    {
        $this->updateData($key, [
            'attempts' => 0
        ]);
    }

    /*
     * Abstract classes
     */

    abstract public function get(string $key);

    abstract public function isBlocked(string $to);

    abstract public function block(string $to);

    abstract public function updateData(string $key, array $updates);
}