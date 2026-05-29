<?php

namespace App\Helpers;

class PhoneHelper
{
    public static function normalizeIndonesianWhatsapp(?string $value): ?string
    {
        return self::sanitizeIndonesianWhatsapp($value);
    }

    public static function sanitizeIndonesianWhatsapp(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $number = preg_replace('/[^\d+]/', '', trim($value));
        $number = preg_replace('/^\+/', '', $number);

        if ($number === '') {
            return null;
        }

        if (!preg_match('/^(08|628)[0-9]{8,13}$/', $number)) {
            return null;
        }

        return $number;
    }
}
