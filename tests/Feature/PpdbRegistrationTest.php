<?php

namespace Tests\Feature;

use App\Models\KontenWeb;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PpdbRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_ppdb_registration_creates_siswa_and_private_documents(): void
    {
        Storage::fake('local');
        KontenWeb::create([
            'tipe' => 'ppdb_tahun_ajaran',
            'judul' => 'Tahun Ajaran PPDB Aktif',
            'konten' => '2027/2028',
        ]);

        $response = $this->postJson(route('api.pendaftaran'), $this->validPayload([
            'nisn' => '0011223344',
            'nis' => 'NIS-REG-001',
        ]));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Pendaftaran berhasil! Silakan cek status pendaftaran Anda nanti.',
            ])
            ->assertJsonStructure(['card_url']);

        $siswa = Siswa::where('nisn', '0011223344')->firstOrFail();

        $this->assertNotNull($siswa->registration_token);
        $this->assertSame('2027/2028', $siswa->tahun_ajaran);
        $this->assertSame('PPDB-2027-0001', $siswa->nomor_pendaftaran);
        $this->assertStringContainsString($siswa->registration_token, $response->json('card_url'));
        $this->assertStringStartsWith('ppdb/', $siswa->file_akte);
        $this->assertStringStartsWith('ppdb/', $siswa->file_kk);
        $this->assertStringStartsWith('ppdb/', $siswa->file_ktp_ortu);
        $this->assertStringStartsWith('ppdb/', $siswa->file_ijazah);

        Storage::disk('local')->assertExists($siswa->file_akte);
        Storage::disk('local')->assertExists($siswa->file_kk);
        Storage::disk('local')->assertExists($siswa->file_ktp_ortu);
        Storage::disk('local')->assertExists($siswa->file_ijazah);
    }

    public function test_public_ppdb_registration_rejects_duplicate_nisn_and_nis(): void
    {
        Storage::fake('local');

        Siswa::create([
            'nama' => 'Pendaftar Lama',
            'nisn' => '0099887766',
            'nis' => 'NIS-DUPLICATE',
            'no_wa' => '081111111111',
        ]);

        $this->postJson(route('api.pendaftaran'), $this->validPayload([
            'nisn' => '0099887766',
            'nis' => 'NIS-DUPLICATE',
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['nisn', 'nis']);

        $this->assertSame(1, Siswa::where('nisn', '0099887766')->count());
        $this->assertCount(0, Storage::disk('local')->allFiles('ppdb'));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Ahmad Daniswara',
            'tempat_lahir' => 'Bogor',
            'tanggal_lahir' => '2018-05-10',
            'jenis_kelamin' => 'Laki-laki',
            'asal_sekolah' => 'TK Annajiyah',
            'nisn' => '0012345678',
            'nis' => 'NIS-001',
            'akte' => 'AKTE-001',
            'kk' => 'KK-001',
            'alamat' => 'Jl. Pendidikan No. 1',
            'ortu' => 'Bapak Ahmad',
            'wa' => '081234567890',
            'file_akte' => UploadedFile::fake()->create('akte.pdf', 24, 'application/pdf'),
            'file_kk' => UploadedFile::fake()->create('kk.pdf', 24, 'application/pdf'),
            'file_ktp' => UploadedFile::fake()->create('ktp.pdf', 24, 'application/pdf'),
            'file_ijazah' => UploadedFile::fake()->create('ijazah.pdf', 24, 'application/pdf'),
        ], $overrides);
    }
}
