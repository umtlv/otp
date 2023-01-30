# OTP - One Time Password

[![Latest Stable Version](http://poser.pugx.org/umtlv/otp/v)](https://packagist.org/packages/umtlv/otp) [![Total Downloads](http://poser.pugx.org/umtlv/otp/downloads)](https://packagist.org/packages/umtlv/otp) [![Latest Unstable Version](http://poser.pugx.org/umtlv/otp/v/unstable)](https://packagist.org/packages/umtlv/otp) [![License](http://poser.pugx.org/umtlv/otp/license)](https://packagist.org/packages/umtlv/otp) [![PHP Version Require](http://poser.pugx.org/umtlv/otp/require/php)](https://packagist.org/packages/umtlv/otp)

### [Open in GitHub](https://github.com/umtlv/otp)

## Installation

<pre>composer require umtlv/otp</pre>

After installing run command below. It will create `otp.php` file in <i>config</i> folder.
<pre>php artisan otp:install</pre>

To use DB storage run command below. It will copy package migrations.
<pre>
php artisan otp:migrations
php artisan migrate
</pre>

## Using

### Creating

```php
$otp = \Axel\Otp\OtpService::create($Key, $Method, $To, $Data);
```

<pre>
$Key - Key for example - SigningUp, ResettingPassword e.g.
$Method - Sending method - mail
$To - E-mail, phone number or another
$Data - Data to save
</pre>

It will return data array with the following keys: <i><u>key</u>, <u>notification_method</u>, <u>notification_to</u>,
<u>ip_address</u>, <u>verify_token</u>, <u>verify_code</u>, <u>expires_at</u>, <u>data</u>, <u>attempts</u>, <u>verified</u></i>.

<pre>
ip_address - user's ip address

verify_token - special token to search this OTP

verify_code - generated code

expires_at - expires time
</pre>

## Getting data
```php
$otp = \Axel\Otp\OtpService::get($VerifyToken);
```
or 
```php
$otp = \Axel\Otp\OtpService::getData($VerifyToken);
```
It will return whole data or data you sent to save as well as when you're creating OTP.

## Checking
```php
$otp = \Axel\Otp\OtpService::check($VerifyToken, $Code);
```
It will return some of this: blocked, expired, wrong_code, success.
You can use ``Axel\Otp\Enum\OtpStatus`` to constant response.

## Deleting
```php
\Axel\Otp\OtpService::delete($VerifyToken);
```

## Custom notification
```php
$otp = new \Axel\Otp\Otp();
$otp->method($Method)
    ->to($To)
    ->data($Data)
    ->send();
```