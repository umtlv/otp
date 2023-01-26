<?php

namespace Axel\Otp\Actions;

use Axel\Otp\Enum\OtpUpdate;
use Axel\Otp\Exceptions\OtpServiceException;
use Axel\Otp\Interfaces\OtpAction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class CacheAction extends Action implements OtpAction
{
    public function save(string $key, array $data)
    {
        $ttl = $this->createTTL($data['expires_at']);
        Cache::put($key, $data, $ttl);
    }

    public function get(string $key)
    {
        return Cache::get($key);
    }

    public function delete(string $key)
    {
        Cache::delete($key);
    }

    /**
     * @throws OtpServiceException
     */
    public function block($to)
    {
        $lockLifetime = $this->getLockLifetime();
        $lockKey = $this->getBlockedKey($to);
        Cache::put($lockKey, true, $lockLifetime);

        if ($this->ipChecking()) {
            $ip = user_ip();
            if (!in_array($ip, ['UNKNOWN', '0.0.0.0'])) {
                $lockKey = $this->getBlockedKey($ip);
                Cache::put($lockKey, true, $lockLifetime);
            }
        }
    }

    public function isBlocked(string $to): bool
    {
        $key = $this->getBlockedKey($to);
        if ($this->get($key)) {
            return true;
        }

        if ($this->ipChecking()) {
            $key = $this->getBlockedKey(user_ip());
            return !!$this->get($key);
        }

        return false;
    }

    private function getBlockedKey(string $key): string
    {
        return "otp_blocked:" . $key;
    }

    public function updateData(string $key, array $updates)
    {
        $data = $this->get($key);
        if (empty($data)) {
            return;
        }

        foreach ($updates as $updateKey => $update) {
            if ($update === OtpUpdate::INCREMENT) {
                $data[$updateKey] = $data[$updateKey] + 1;
                continue;
            }

            $data[$updateKey] = $update;
        }

        $ttl = $this->createTTL($data['expires_at']);
        Cache::put($key, $data, $ttl);
    }

    private function createTTL(string $datetime)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $datetime);
    }
}