<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Ahmad Daniswara');

        $this->get('/pendaftaran/cetak/' . $siswa->id)
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
}
