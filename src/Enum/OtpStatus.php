<?php

namespace Axel\Otp\Enum;

class OtpStatus
{
    const BLOCKED = 'blocked';
    const EXPIRED = 'expired';
    const WRONG_CODE = 'wrong_code';
    const SUCCESS = 'success';
}