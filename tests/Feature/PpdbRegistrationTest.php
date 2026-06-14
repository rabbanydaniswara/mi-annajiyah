<?php

namespace Tests\Feature;

use App\Models\KontenWeb;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
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
        $this->assertSame('081234567890', $siswa->no_wa);
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

    public function test_closed_ppdb_hides_form_and_rejects_direct_submission(): void
    {
        Storage::fake('local');
        KontenWeb::create([
            'tipe' => 'ppdb_status',
            'judul' => 'Status Pendaftaran PPDB',
            'konten' => 'closed',
        ]);
        KontenWeb::create([
            'tipe' => 'ppdb_pesan_tutup',
            'judul' => 'Pesan Publik Saat PPDB Ditutup',
            'konten' => 'PPDB ditutup sampai pengumuman gelombang berikutnya.',
        ]);

        $this->get(route('pendaftaran'))
            ->assertOk()
            ->assertSee('Pendaftaran Ditutup')
            ->assertSee('PPDB ditutup sampai pengumuman gelombang berikutnya.')
            ->assertDontSee('id="formPendaftaran"', false);

        $this->postJson(route('api.pendaftaran'), $this->validPayload())
            ->assertForbidden()
            ->assertJson([
                'success' => false,
                'code' => 'ppdb_closed',
                'message' => 'PPDB ditutup sampai pengumuman gelombang berikutnya.',
            ]);

        $this->assertDatabaseCount('siswa', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles('ppdb'));
    }

    public function test_ppdb_is_open_by_default_when_status_setting_does_not_exist(): void
    {
        $this->get(route('pendaftaran'))
            ->assertOk()
            ->assertSee('id="formPendaftaran"', false)
            ->assertSee('Formulir Pendaftaran');
    }

    public function test_public_ppdb_registration_rejects_invalid_whatsapp_number(): void
    {
        Storage::fake('local');

        $this->postJson(route('api.pendaftaran'), $this->validPayload([
            'wa' => '12345',
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['wa']);

        $this->assertDatabaseCount('siswa', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles('ppdb'));
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

    public function test_public_ppdb_registration_rejects_invalid_field_formats(): void
    {
        Storage::fake('local');
        $this->withoutMiddleware(ThrottleRequests::class);

        $invalidCases = [
            'nama berisi angka' => ['nama', 'Ahmad 123'],
            'tempat lahir berisi angka' => ['tempat_lahir', 'Bogor 2'],
            'tanggal lahir bukan masa lalu' => ['tanggal_lahir', now()->toDateString()],
            'pilihan jenis kelamin tidak dikenal' => ['jenis_kelamin', 'Lainnya'],
            'asal sekolah berisi markup' => ['asal_sekolah', '<script>alert(1)</script>'],
            'NISN kurang dari sepuluh digit' => ['nisn', '123456789'],
            'NIS berisi karakter terlarang' => ['nis', 'NIS@001'],
            'nomor akte berisi karakter terlarang' => ['akte', 'AKTE@001'],
            'nomor KK bukan enam belas digit' => ['kk', '123456789'],
            'alamat terlalu pendek' => ['alamat', 'Bogor'],
            'nama orang tua berisi angka' => ['ortu', 'Bapak 123'],
            'WhatsApp bukan nomor Indonesia' => ['wa', '12345'],
        ];

        foreach ($invalidCases as $case => [$field, $value]) {
            $this->postJson(route('api.pendaftaran'), $this->validPayload([
                $field => $value,
            ]))->assertUnprocessable()
                ->assertJsonValidationErrors([$field]);
        }

        $this->assertDatabaseCount('siswa', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles('ppdb'));
    }

    public function test_public_ppdb_registration_rejects_invalid_document_files(): void
    {
        Storage::fake('local');

        $this->postJson(route('api.pendaftaran'), $this->validPayload([
            'file_akte' => UploadedFile::fake()->create('akte.txt', 24, 'text/plain'),
            'file_kk' => UploadedFile::fake()->create('kk.pdf', 5121, 'application/pdf'),
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['file_akte', 'file_kk']);

        $this->assertDatabaseCount('siswa', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles('ppdb'));
    }

    public function test_public_ppdb_registration_accepts_common_name_punctuation_and_normalizes_whitespace(): void
    {
        Storage::fake('local');

        $this->postJson(route('api.pendaftaran'), $this->validPayload([
            'nama' => "  Siti   Nur 'Aini  ",
            'tempat_lahir' => '  Tangerang   Selatan ',
            'asal_sekolah' => ' TK Islam 01 (Annajiyah) ',
            'ortu' => '  Muhammad   Al-Fatih ',
            'alamat' => "  Jl. Pendidikan   No. 1\n RT 01 / RW 02 ",
            'wa' => '+6281234567890',
        ]))->assertOk();

        $this->assertDatabaseHas('siswa', [
            'nama' => "Siti Nur 'Aini",
            'tempat_lahir' => 'Tangerang Selatan',
            'asal_sekolah' => 'TK Islam 01 (Annajiyah)',
            'nama_ortu' => 'Muhammad Al-Fatih',
            'alamat' => "Jl. Pendidikan No. 1\nRT 01 / RW 02",
            'no_wa' => '6281234567890',
        ]);
    }

    public function test_public_ppdb_registration_removes_uploaded_documents_when_database_write_fails(): void
    {
        Storage::fake('local');
        Event::listen('eloquent.creating: App\Models\Siswa', function (): void {
            throw new RuntimeException('Simulated database failure');
        });

        $this->postJson(route('api.pendaftaran'), $this->validPayload())
            ->assertInternalServerError()
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('siswa', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles('ppdb'));
        $this->assertCount(0, Storage::disk('local')->allFiles('ppdb-thumbs'));
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
            'kk' => '3674010203040001',
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
