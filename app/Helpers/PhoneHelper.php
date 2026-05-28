<?php

namespace App\Helpers;

class PhoneHelper
{
    public static function normalizeIndonesianWhatsapp(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $number = preg_replace('/[^\d+]/', '', trim($value));
        $number = preg_replace('/^\+/', '', $number);

        if ($number === '') {
            return null;
        }

        if (str_starts_with($number, '08')) {
            $number = '62' . substr($number, 1);
        } elseif (str_starts_with($number, '8')) {
            $number = '62' . $number;
        } elseif (str_starts_with($number, '620')) {
            $number = '62' . substr($number, 3);
        }

        if (!preg_match('/^628[0-9]{8,13}$/', $number)) {
            return null;
        }

        return $number;
    }
}
