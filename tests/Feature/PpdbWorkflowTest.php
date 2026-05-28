<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PpdbWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_ppdb_status_with_internal_note(): void
    {
        $admin = User::create([
            'username' => 'admin-ppdb-workflow',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);
        $siswa = Siswa::create([
            'nama' => 'Ahmad Verifikasi',
            'status_ppdb' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.ppdb.updateStatus'), [
                'id' => $siswa->id,
                'status' => 'berkas_kurang',
                'catatan_verifikasi' => 'KTP wali belum jelas.',
            ])
            ->assertRedirect(route('admin.ppdb'));

        $siswa->refresh();
        $this->assertSame('berkas_kurang', $siswa->status_ppdb);
        $this->assertSame('KTP wali belum jelas.', $siswa->catatan_verifikasi);
        $this->assertNotNull($siswa->tgl_verifikasi);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'update_status',
            'model_type' => 'Siswa',
            'model_id' => $siswa->id,
        ]);
    }

    public function test_admin_can_bulk_update_ppdb_status(): void
    {
        $admin = User::create([
            'username' => 'admin-ppdb-bulk',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);
        $first = Siswa::create(['nama' => 'Ahmad Satu', 'status_ppdb' => 'pending']);
        $second = Siswa::create(['nama' => 'Ahmad Dua', 'status_ppdb' => 'pending']);

        $this->actingAs($admin)
            ->post(route('admin.ppdb.bulkUpdateStatus'), [
                'ids' => [$first->id, $second->id],
                'status' => 'daftar_ulang',
                'catatan_verifikasi' => 'Berkas sudah lengkap.',
            ])
            ->assertRedirect(route('admin.ppdb'));

        $this->assertSame(2, Siswa::where('status_ppdb', 'daftar_ulang')->count());
        $this->assertSame(2, ActivityLog::where('action', 'bulk_update_status')->count());
        $this->assertDatabaseHas('siswa', [
            'id' => $first->id,
            'catatan_verifikasi' => 'Berkas sudah lengkap.',
        ]);
    }

    public function test_public_check_status_uses_registration_number_and_hides_internal_note(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Ahmad Daniswara',
            'status_ppdb' => 'berkas_kurang',
            'catatan_verifikasi' => 'KTP wali belum jelas.',
        ]);

        $this->get(route('cek-pendaftaran', ['q' => $siswa->nomor_pendaftaran]))
            ->assertOk()
            ->assertSee('Berkas Perlu Dilengkapi')
            ->assertSee($siswa->nomor_pendaftaran)
            ->assertDontSee('KTP wali belum jelas.');
    }

    public function test_admin_ppdb_filter_by_status_class_and_date(): void
    {
        $admin = User::create([
            'username' => 'admin-ppdb-filter',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);
        Siswa::create([
            'nama' => 'Masuk Filter',
            'kelas' => '1A',
            'status_ppdb' => 'diverifikasi',
            'tanggal_daftar' => '2026-05-10 08:00:00',
        ]);
        Siswa::create([
            'nama' => 'Keluar Filter',
            'kelas' => '1B',
            'status_ppdb' => 'pending',
            'tanggal_daftar' => '2026-05-20 08:00:00',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.ppdb', [
                'status' => 'diverifikasi',
                'kelas' => '1A',
                'tanggal_dari' => '2026-05-01',
                'tanggal_sampai' => '2026-05-15',
            ]))
            ->assertOk()
            ->assertSee('Masuk Filter')
            ->assertDontSee('Keluar Filter');
    }
}
