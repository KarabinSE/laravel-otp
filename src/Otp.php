<?php

namespace Ichtrojan\Otp;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Config;

class Otp
{
    public $model;

    public function __construct() {
        $this->model = Config::get('otp.model');
    }

    /**
     * Generate a new OTP
     */
    public function generate(string $identifier, string $type, int $length = null, int $validity = null): object
    {
        $length = $length ?? Config::get('otp.length', 6);
        $validity = $validity ?? Config::get('otp.expiry', 10);

        // Remove existing valid tokens for this identifier
        $this->model->where('identifier', $identifier)->where('valid', true)->delete();

        switch ($type) {
            case "numeric":
                $token = $this->generateNumericToken($length);
                break;
            case "alpha_numeric":
                $token = $this->generateAlphanumericToken($length);
                break;
            default:
                throw new Exception("{$type} is not a supported type");
        }

        $this->model->create([
            'identifier' => $identifier,
            'token' => $token,
            'validity' => $validity
        ]);

        return (object) [
            'status' => true,
            'token' => $token,
            'message' => 'OTP generated'
        ];
    }

    /**
     * Check if the OTP is valid without invalidating it
     */
    public function isValid(string $identifier, string $token): bool
    {
        $otp = $this->model->where('identifier', $identifier)->where('token', $token)->first();

        if ($otp instanceof Model) {
            $validUntil = $otp->created_at->addMinutes($otp->validity);
            return Carbon::now()->lt($validUntil) && $otp->valid;
        }

        return false;
    }

    /**
     * Validate and invalidate the OTP
     */
    public function validate(string $identifier, string $token): object
    {
        $otp = $this->model->where('identifier', $identifier)->where('token', $token)->first();

        if (!$otp instanceof Model) {
            return (object) [
                'status' => false,
                'message' => 'OTP does not exist'
            ];
        }

        if (!$otp->valid) {
            return (object) [
                'status' => false,
                'message' => 'OTP is not valid'
            ];
        }

        $otp->update(['valid' => false]); // Always invalidate after attempt

        $validUntil = $otp->created_at->addMinutes($otp->validity);
        if (Carbon::now()->gt($validUntil)) {
            return (object) [
                'status' => false,
                'message' => 'OTP expired'
            ];
        }

        return (object) [
            'status' => true,
            'message' => 'OTP is valid'
        ];
    }

    /**
     * Generate a numeric token
     */
    private function generateNumericToken(int $length = 6): string
    {
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= random_int(0, 9);
        }
        return $token;
    }

    /**
     * Generate an alphanumeric token
     */
    private function generateAlphanumericToken(int $length = 6): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($characters, $length)), 0, $length);
    }

}
