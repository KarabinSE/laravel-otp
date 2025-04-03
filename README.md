# Laravel OTP ▲

[![Latest Stable Version](https://poser.pugx.org/ichtrojan/laravel-otp/v/stable)](https://packagist.org/packages/ichtrojan/laravel-otp) [![Total Downloads](https://poser.pugx.org/ichtrojan/laravel-otp/downloads)](https://packagist.org/packages/ichtrojan/laravel-otp) [![License](https://poser.pugx.org/ichtrojan/laravel-otp/license)](https://packagist.org/packages/ichtrojan/laravel-otp)

## Introduction 🖖

This is a simple package to generate and validate OTPs (One Time Passwords). It can be used in authentication systems, two-factor verification, and more.

---

## Installation

Install via composer:

```bash
composer require ichtrojan/laravel-otp
```

## One-Step Install (Recommended)

Run this command to publish both the config and migration files:

```bash
php artisan otp:install
```

Then Run

```bash
php artisan migrate
```

## Configuration

You can customize OTP behavior by editing the published config file:

```bash
php artisan vendor:publish --tag=otp-config
```

config/otp.php:

```php
return [
    'table' => env('OTP_TABLE', 'otps'), // OTP table name
    'length' => 6,                        // OTP digit/character length
    'expiry' => 10,                       // Validity in minutes
    'max_attempts' => 3,                  // Optional: max retries (for future use)
];
```

## Usage 🧨

> **NOTE**</br>
> Response are returned as objects. You can access its attributes with the arrow operator (`->`)

### Generate OTP

```php
<?php

use Ichtrojan\Otp\Otp;

(new Otp)->generate(string $identifier, string $type, int $length = 4, int $validity = 10);
```

- `$identifier`: The identity that will be tied to the OTP.
- `$type`: The type of token to be generated, supported types are `numeric` and `alpha_numeric`
- `$length (optional | default = 4)`: The length of token to be generated.
- `$validity (optional | default = 10)`: The validity period of the OTP in minutes.

#### Sample

```php
<?php

use Ichtrojan\Otp\Otp;

(new Otp)->generate('michael@okoh.co.uk', 'numeric', 6, 15);
```

This will generate a six digit OTP that will be valid for 15 minutes and the success response will be:

```object
{
  "status": true,
  "token": "282581",
  "message": "OTP generated"
}
```

### Validate OTP

```php
<?php

use Ichtrojan\Otp\Otp;

(new Otp)->validate(string $identifier, string $token)
```

- `$identifier`: The identity that is tied to the OTP.
- `$token`: The token tied to the identity.

#### Sample

```php
<?php

use Ichtrojan\Otp\Otp;

(new Otp)->validate('michael@okoh.co.uk', '282581');
```

#### Responses

**On Success**

```object
{
  "status": true,
  "message": "OTP is valid"
}
```

**Does not exist**

```object
{
  "status": false,
  "message": "OTP does not exist"
}
```

**Not Valid\***

```object
{
  "status": false,
  "message": "OTP is not valid"
}
```

**Expired**

```object
{
  "status": false,
  "message": "OTP Expired"
}
```

### Check The validity of an OTP

To verify the validity of an OTP without marking it as used, you can use the `isValid` method:

```php
<?php
use Ichtrojan\Otp\Otp;

(new Otp)->isValid(string $identifier, string $token);
```

This will return a boolean value of the validity of the OTP.

### Delete expired tokens

You can delete expired tokens by running the following artisan command:

```bash
php artisan otp:clean
```

You can also add this artisan command to `app/Console/Kernel.php` to automatically clean on scheduled

```php
<?php

protected function schedule(Schedule $schedule)
{
    $schedule->command('otp:clean')->daily();
}
```

## Contribution

If you find an issue with this package or you have any suggestion please help out. I am not perfect.
