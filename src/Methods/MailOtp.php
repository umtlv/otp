<?php

namespace Axel\Otp\Methods;

use Axel\Otp\Exceptions\InvalidMethodParametersException;
use Axel\Otp\Interfaces\OtpMethod;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class MailOtp implements OtpMethod
{
    private $Mail;

    public function __construct()
    {
        $this->Mail = new Mailable();
    }

    public function to(string $to)
    {
        $this->Mail->to($to);
    }

    /**
     * @throws InvalidMethodParametersException
     */
    public function data(array $data)
    {
        if (!array_key_exists('type', $data)) {
            $type = 'text';
        } else {
            $type = $data['type'];
        }

        if (!array_key_exists('body', $data)) {
            throw new InvalidMethodParametersException('Mail\'s body parameter does not exist');
        }

        if (!array_key_exists('subject', $data)) {
            throw new InvalidMethodParametersException('Mail\'s subject parameter does not exist');
        }

        $this->Mail->subject($data['subject']);

        if ($data['type'] === 'html') {
            $this->Mail->html($data['body']);
        } elseif ($data['type'] === 'text') {
            $this->Mail->text($data['body']);
        }
    }

    public function send()
    {
        Mail::send($this->Mail);
    }
}