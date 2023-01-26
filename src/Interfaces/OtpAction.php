<?php

namespace Axel\Otp\Interfaces;

interface OtpAction
{
    public function save(string $key, array $data);

    public function get(string $key);

    public function isBlocked(string $to);

    public function block(string $to);

    public function delete(string $key);
}