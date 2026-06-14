<?php

namespace App\Http\Requests;

use App\Support\SiswaInputRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama' => SiswaInputRules::normalizeSingleLine($this->input('nama')),
            'tempat_lahir' => SiswaInputRules::normalizeSingleLine($this->input('tempat_lahir')),
            'nisn' => SiswaInputRules::normalizeSingleLine($this->input('nisn')),
            'nis' => SiswaInputRules::normalizeSingleLine($this->input('nis')),
            'kelas' => SiswaInputRules::normalizeSingleLine($this->input('kelas')),
            'no_wa' => SiswaInputRules::normalizeSingleLine($this->input('no_wa')),
            'alamat' => SiswaInputRules::normalizeAddress($this->input('alamat')),
            'asal_sekolah' => SiswaInputRules::normalizeSingleLine($this->input('asal_sekolah')),
            'nama_ortu' => SiswaInputRules::normalizeSingleLine($this->input('nama_ortu')),
        ]);
    }

    public function rules(): array
    {
        $id = $this->integer('id') ?: null;

        return [
            'id' => ['nullable', 'integer', 'exists:siswa,id'],
            'nama' => SiswaInputRules::personName(),
            'tempat_lahir' => SiswaInputRules::place(false),
            'tanggal_lahir' => ['nullable', 'date_format:Y-m-d', 'before:today'],
            'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'nisn' => SiswaInputRules::nisn($id),
            'nis' => SiswaInputRules::nis($id),
            'kelas' => ['nullable', 'string', 'min:1', 'max:10', 'regex:/^[A-Za-z0-9 -]+$/'],
            'no_wa' => SiswaInputRules::whatsapp(false),
            'alamat' => SiswaInputRules::address(false),
            'asal_sekolah' => SiswaInputRules::school(false),
            'nama_ortu' => SiswaInputRules::personName(false),
        ];
    }

    public function messages(): array
    {
        return array_merge(SiswaInputRules::messages(), [
            'kelas.regex' => 'Kelas hanya boleh berisi huruf, angka, spasi, dan tanda hubung.',
        ]);
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama siswa',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'jenis_kelamin' => 'jenis kelamin',
            'nisn' => 'NISN',
            'nis' => 'NIS',
            'kelas' => 'kelas',
            'no_wa' => 'nomor WhatsApp',
            'alamat' => 'alamat',
            'asal_sekolah' => 'asal sekolah',
            'nama_ortu' => 'nama orang tua/wali',
        ];
    }
}
