<?php

namespace App\Support;

use App\Helpers\PhoneHelper;
use Closure;
use Illuminate\Validation\Rule;

final class SiswaInputRules
{
    private const PERSON_NAME_PATTERN = "/^[\p{L}\p{M} .'\x{2019}-]+$/u";

    private const PLACE_PATTERN = "/^[\p{L}\p{M} .,'\x{2019}()\/-]+$/u";

    private const SCHOOL_PATTERN = "/^[\p{L}\p{M}\p{N} .,'\x{2019}&()\/-]+$/u";

    private const ADDRESS_PATTERN = "/^[\p{L}\p{M}\p{N}\s.,'\x{2019}#&()\/:-]+$/u";

    private const DOCUMENT_NUMBER_PATTERN = '/^[A-Za-z0-9 .\/-]+$/';

    private const STUDENT_NUMBER_PATTERN = '/^[A-Za-z0-9.\/-]+$/';

    public static function personName(bool $required = true): array
    {
        return [
            'bail',
            $required ? 'required' : 'nullable',
            'string',
            'min:3',
            'max:100',
            'regex:'.self::PERSON_NAME_PATTERN,
        ];
    }

    public static function place(bool $required = true): array
    {
        return [
            'bail',
            $required ? 'required' : 'nullable',
            'string',
            'min:2',
            'max:100',
            'regex:'.self::PLACE_PATTERN,
        ];
    }

    public static function school(bool $required = true): array
    {
        return [
            'bail',
            $required ? 'required' : 'nullable',
            'string',
            'min:2',
            'max:150',
            'regex:'.self::SCHOOL_PATTERN,
        ];
    }

    public static function nisn(?int $ignoreId = null): array
    {
        return [
            'bail',
            'nullable',
            'digits:10',
            Rule::unique('siswa', 'nisn')->ignore($ignoreId),
        ];
    }

    public static function nis(?int $ignoreId = null): array
    {
        return [
            'bail',
            'nullable',
            'string',
            'min:3',
            'max:20',
            'regex:'.self::STUDENT_NUMBER_PATTERN,
            Rule::unique('siswa', 'nis')->ignore($ignoreId),
        ];
    }

    public static function documentNumber(bool $required = true): array
    {
        return [
            'bail',
            $required ? 'required' : 'nullable',
            'string',
            'min:3',
            'max:50',
            'regex:'.self::DOCUMENT_NUMBER_PATTERN,
        ];
    }

    public static function familyCardNumber(bool $required = true): array
    {
        return [
            'bail',
            $required ? 'required' : 'nullable',
            'digits:16',
        ];
    }

    public static function address(bool $required = true): array
    {
        return [
            'bail',
            $required ? 'required' : 'nullable',
            'string',
            'min:10',
            'max:1000',
            'regex:'.self::ADDRESS_PATTERN,
        ];
    }

    public static function whatsapp(bool $required = true): array
    {
        return [
            'bail',
            $required ? 'required' : 'nullable',
            'string',
            'max:30',
            function (string $attribute, mixed $value, Closure $fail): void {
                if ($value !== null && $value !== '' && ! PhoneHelper::sanitizeIndonesianWhatsapp((string) $value)) {
                    $fail('Nomor WhatsApp harus memakai format Indonesia yang valid, contoh: 081234567890.');
                }
            },
        ];
    }

    public static function normalizeSingleLine(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = preg_replace('/\s+/u', ' ', trim($value));

        return $normalized === '' ? null : $normalized;
    }

    public static function normalizeAddress(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = str_replace(["\r\n", "\r"], "\n", trim($value));
        $lines = array_map(
            fn (string $line) => preg_replace('/[ \t]+/u', ' ', trim($line)),
            explode("\n", $value)
        );
        $normalized = trim(implode("\n", array_filter($lines, fn (string $line) => $line !== '')));

        return $normalized === '' ? null : $normalized;
    }

    public static function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'min' => ':attribute minimal :min karakter.',
            'max' => ':attribute maksimal :max karakter.',
            'digits' => ':attribute harus terdiri dari tepat :digits digit angka.',
            'date_format' => 'Format :attribute tidak valid.',
            'before' => ':attribute harus berupa tanggal sebelum hari ini.',
            'in' => 'Pilihan :attribute tidak valid.',
            'unique' => ':attribute sudah terdaftar sebelumnya.',
            'regex' => 'Format :attribute tidak valid.',
            'file' => ':attribute harus berupa file.',
            'mimes' => ':attribute harus berformat JPG, JPEG, PNG, atau PDF.',
            'mimetypes' => 'Isi :attribute tidak sesuai dengan jenis file yang diizinkan.',
            'file_akte.max' => 'File akte maksimal 5 MB.',
            'file_kk.max' => 'File Kartu Keluarga maksimal 5 MB.',
            'file_ktp.max' => 'File KTP orang tua maksimal 5 MB.',
            'file_ijazah.max' => 'File ijazah maksimal 5 MB.',
            'nama.regex' => 'Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
            'ortu.regex' => 'Nama orang tua/wali hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
            'nama_ortu.regex' => 'Nama orang tua/wali hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
            'tempat_lahir.regex' => 'Tempat lahir hanya boleh berisi huruf dan tanda baca yang wajar.',
            'asal_sekolah.regex' => 'Nama sekolah mengandung karakter yang tidak diizinkan.',
            'nis.regex' => 'NIS hanya boleh berisi huruf, angka, titik, garis miring, dan tanda hubung.',
            'akte.regex' => 'Nomor akte hanya boleh berisi huruf, angka, spasi, titik, garis miring, dan tanda hubung.',
            'alamat.regex' => 'Alamat mengandung karakter yang tidak diizinkan.',
        ];
    }
}
