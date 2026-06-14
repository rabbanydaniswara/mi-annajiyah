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

        if (! preg_match('/^(?:08[0-9]{8,11}|628[0-9]{8,11})$/', $number)) {
            return null;
        }

        return $number;
    }

    public static function whatsappUrl(?string $value): ?string
    {
        $number = self::sanitizeIndonesianWhatsapp($value);

        if ($number === null) {
            return null;
        }

        if (str_starts_with($number, '08')) {
            $number = '62'.substr($number, 1);
        }

        return 'https://wa.me/'.$number;
    }
}
