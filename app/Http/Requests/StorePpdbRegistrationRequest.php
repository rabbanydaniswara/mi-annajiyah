<?php

namespace App\Http\Requests;

use App\Support\SiswaInputRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePpdbRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama' => SiswaInputRules::normalizeSingleLine($this->input('nama')),
            'tempat_lahir' => SiswaInputRules::normalizeSingleLine($this->input('tempat_lahir')),
            'asal_sekolah' => SiswaInputRules::normalizeSingleLine($this->input('asal_sekolah')),
            'nisn' => SiswaInputRules::normalizeSingleLine($this->input('nisn')),
            'nis' => SiswaInputRules::normalizeSingleLine($this->input('nis')),
            'akte' => SiswaInputRules::normalizeSingleLine($this->input('akte')),
            'kk' => SiswaInputRules::normalizeSingleLine($this->input('kk')),
            'alamat' => SiswaInputRules::normalizeAddress($this->input('alamat')),
            'ortu' => SiswaInputRules::normalizeSingleLine($this->input('ortu')),
            'wa' => SiswaInputRules::normalizeSingleLine($this->input('wa')),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => SiswaInputRules::personName(),
            'tempat_lahir' => SiswaInputRules::place(),
            'tanggal_lahir' => ['bail', 'required', 'date_format:Y-m-d', 'before:today'],
            'jenis_kelamin' => ['bail', 'required', Rule::in(['Laki-laki', 'Perempuan'])],
            'asal_sekolah' => SiswaInputRules::school(),
            'nisn' => SiswaInputRules::nisn(),
            'nis' => SiswaInputRules::nis(),
            'akte' => SiswaInputRules::documentNumber(),
            'kk' => SiswaInputRules::familyCardNumber(),
            'alamat' => SiswaInputRules::address(),
            'ortu' => SiswaInputRules::personName(),
            'wa' => SiswaInputRules::whatsapp(),
            'file_akte' => ['bail', 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120'],
            'file_kk' => ['bail', 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120'],
            'file_ktp' => ['bail', 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120'],
            'file_ijazah' => ['bail', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return SiswaInputRules::messages();
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama lengkap anak',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'jenis_kelamin' => 'jenis kelamin',
            'asal_sekolah' => 'asal sekolah',
            'nisn' => 'NISN',
            'nis' => 'NIS',
            'akte' => 'nomor akte kelahiran',
            'kk' => 'nomor Kartu Keluarga',
            'alamat' => 'alamat lengkap',
            'ortu' => 'nama orang tua/wali',
            'wa' => 'nomor WhatsApp',
            'file_akte' => 'file akte kelahiran',
            'file_kk' => 'file Kartu Keluarga',
            'file_ktp' => 'file KTP orang tua/wali',
            'file_ijazah' => 'file ijazah',
        ];
    }
}
