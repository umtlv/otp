<?php

namespace Axel\Otp\Storages;

use Axel\Otp\Enum\OtpUpdate;
use Axel\Otp\Exceptions\OtpServiceException;
use Axel\Otp\Interfaces\OtpAction;
use Axel\Otp\Models\OtpBlacklist;
use Axel\Otp\Models\OtpModel;

class TableAction extends Action implements OtpAction
{
    public function save(string $key, array $data)
    {
        OtpModel::query()->create($data);
    }

    public function get(string $key): ?array
    {
        $data = OtpModel::query()->where('verify_token', $key)->first();
        if ($data->getAttribute('expires_at') > now()->toDateTimeString())
            return null;

        return empty($data) ? null : $data->toArray();
    }

    public function getOtp(string $key)
    {
        return OtpModel::query()->where('verify_token', $key)->first();
    }

    /**
     * @throws OtpServiceException
     */
    public function block(string $to)
    {
        $data = [
            'to'         => $to,
            'expires_at' => $this->getLockLifetime()
        ];

        if ($this->ipChecking()) {
            $ip = user_ip();
            if (!in_array($ip, ['UNKNOWN', '0.0.0.0'])) {
                $data['ip_address'] = $ip;
            }
        }

        OtpBlacklist::query()->create($data);
    }

    public function delete(string $key)
    {
        $otp = $this->getOtp($key);
        $otp->delete();
    }

    public function isBlocked(string $to): bool
    {
        $search = OtpBlacklist::query()->where('notification_to', $to)->first();

        if (empty($search)) {
            return false;
        }

        $now = now()->toDateTimeString();
        if ($search->expires_at < $now) {
            return true;
        }
        $search->delete();

        if ($this->ipChecking()) {
            $search = OtpBlacklist::query()->where('ip_address', user_ip())->first();
            if ($search) {
                if ($search < $now) {
                    return true;
                }
                $search->delete();
            }
        }

        return false;
    }

    public function updateData(string $key, array $updates)
    {
        /** @var OtpModel $otp */
        $otp = $this->getOtp($key);
        if (empty($otp)) {
            return;
        }

        foreach ($updates as $updateKey => $update) {
            if ($update === OtpUpdate::INCREMENT) {
                $otp->{$updateKey} = $otp->{$updateKey} + 1;
                continue;
            }

            $otp->{$updateKey} = $update;
        }

        $otp->save();
    }
}