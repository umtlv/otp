<?php

namespace Axel\Otp\Models;

use Illuminate\Database\Eloquent\Model;

class OtpModel extends Model
{
    protected $table = 'otp';
    protected $casts = [
        'data'       => 'array',
        'verified'   => 'boolean',
        'attempts'   => 'integer',
        'expires_at' => 'datetime'
    ];
}