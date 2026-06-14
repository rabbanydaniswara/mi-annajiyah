<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PpdbSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_print_card_requires_registration_token(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Ahmad Daniswara',
            'nisn' => '0012345678',
            'nis' => 'NIS-001',
            'no_wa' => '081234567890',
            'asal_sekolah' => 'TK Annajiyah',
        ]);

        $this->get(route('pendaftaran.print', $siswa->registration_token))
            ->assertOk()
            ->assertSee('Ahmad Daniswara')
            ->assertSee($siswa->nomor_pendaftaran)
            ->assertDontSee('REG-'.str_pad($siswa->id, 4, '0', STR_PAD_LEFT));

        $this->get('/pendaftaran/cetak/'.$siswa->id)
            ->assertNotFound();
    }

    public function test_public_check_status_hides_sensitive_registration_data(): void
    {
        Siswa::create([
            'nama' => 'Ahmad Daniswara',
            'nisn' => '0099999999',
            'nis' => 'NIS-PRIVATE',
            'no_wa' => '081234567890',
            'asal_sekolah' => 'TK Annajiyah',
        ]);

        $this->get(route('cek-pendaftaran', ['q' => 'NIS-PRIVATE']))
            ->assertOk()
            ->assertSee('Status Pendaftaran')
            ->assertSee('A**** D******')
            ->assertDontSee('Ahmad Daniswara')
            ->assertDontSee('0099999999')
            ->assertDontSee('081234567890')
            ->assertDontSee('TK Annajiyah');
    }

    public function test_ppdb_document_route_requires_authenticated_admin_area(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('ppdb/akte-test.pdf', 'fake pdf content');

        $siswa = Siswa::create([
            'nama' => 'Ahmad Daniswara',
            'file_akte' => 'ppdb/akte-test.pdf',
        ]);

        $this->get('/storage/ppdb/akte-test.pdf')
            ->assertForbidden();

        $this->get(route('admin.ppdb.document', ['siswa' => $siswa, 'field' => 'file_akte']))
            ->assertRedirect(route('admin.login'));

        $admin = User::create([
            'username' => 'admin-test',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.ppdb.document', ['siswa' => $siswa, 'field' => 'file_akte']))
            ->assertOk();
    }

    public function test_ppdb_image_document_thumbnail_requires_auth_and_stays_private(): void
    {
        Storage::fake('local');
        Storage::disk('local')->putFileAs('ppdb', UploadedFile::fake()->image('akte.png', 640, 480), 'akte-test.png');

        $siswa = Siswa::create([
            'nama' => 'Ahmad Daniswara',
            'file_akte' => 'ppdb/akte-test.png',
        ]);

        $route = route('admin.ppdb.document.thumbnail', ['siswa' => $siswa, 'field' => 'file_akte']);

        $this->get($route)
            ->assertRedirect(route('admin.login'));

        $admin = User::create([
            'username' => 'admin-thumb',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get($route)
            ->assertOk()
            ->assertHeader('Content-Type', 'image/webp');

        $thumbs = Storage::disk('local')->files('ppdb-thumbs');
        $this->assertCount(1, $thumbs);
    }

    public function test_legacy_public_ppdb_documents_can_be_migrated_to_private_storage(): void
    {
        Storage::fake('local');
        File::ensureDirectoryExists(public_path('uploads'));
        $legacyFile = UploadedFile::fake()->image('akte-public.png', 640, 480);
        File::copy(
            $legacyFile->getRealPath(),
            public_path('uploads/akte-public-test.png')
        );

        $siswa = Siswa::create([
            'nama' => 'Legacy PPDB',
            'file_akte' => 'uploads/akte-public-test.png',
        ]);

        $this->artisan('ppdb:migrate-public-documents')
            ->assertExitCode(0);

        $siswa->refresh();

        $this->assertStringStartsWith('ppdb/', $siswa->file_akte);
        $this->assertFileDoesNotExist(public_path('uploads/akte-public-test.png'));
        Storage::disk('local')->assertExists($siswa->file_akte);
        $this->assertNotEmpty(Storage::disk('local')->files('ppdb-thumbs'));
    }
}
