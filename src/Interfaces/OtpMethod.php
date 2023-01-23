<?php

namespace Axel\Otp\Interfaces;

interface OtpMethod
{
    public function to(string $to);

    public function data(array $data);

    public function send();
}