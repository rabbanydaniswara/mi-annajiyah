<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Guru;
use App\Models\KegiatanKategori;
use App\Models\KegiatanSekolah;
use App\Models\KontenWeb;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_main_web_content(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.konten.update'), [
                'tipe' => 'visi',
                'konten' => 'Menjadi madrasah unggul dan berakhlak.',
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'visi']));

        $this->assertDatabaseHas('konten_web', [
            'tipe' => 'visi',
            'konten' => 'Menjadi madrasah unggul dan berakhlak.',
        ]);
    }

    public function test_admin_can_create_and_delete_activity_content(): void
    {
        $admin = $this->adminUser();
        $kategori = KegiatanKategori::create(['nama' => 'Kegiatan Test', 'warna' => 'green']);

        $this->actingAs($admin)
            ->post(route('admin.konten.storeKegiatan'), [
                'judul' => 'Manasik Haji Cilik',
                'deskripsi' => 'Kegiatan pembelajaran praktik.',
                'tanggal' => '2026-05-20',
                'kategori_id' => $kategori->id,
            ])
            ->assertRedirect(route('admin.konten'));

        $kegiatan = KegiatanSekolah::where('judul', 'Manasik Haji Cilik')->firstOrFail();
        $this->actingAs($admin)
            ->delete(route('admin.konten.destroyKegiatan', $kegiatan->id))
            ->assertRedirect(route('admin.konten'));

        $this->assertDatabaseMissing('kegiatan_sekolah', [
            'id' => $kegiatan->id,
        ]);
    }

    public function test_admin_can_manage_guru_and_fasilitas_without_media_upload(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.guru.store'), [
                'nama' => 'Ustadzah Test',
                'mapel' => 'Akidah Akhlak',
                'jabatan' => 'Guru',
                'tampilkan' => '1',
            ])
            ->assertRedirect(route('admin.guru'));

        $guru = Guru::where('nama', 'Ustadzah Test')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.fasilitas.store'), [
                'nama' => 'Ruang Literasi',
                'deskripsi' => 'Area membaca siswa.',
                'ikon' => 'fa-book-open',
                'aktif' => '1',
            ])
            ->assertRedirect(route('admin.fasilitas'));

        $fasilitas = Fasilitas::where('nama', 'Ruang Literasi')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.guru.destroy', $guru->id))
            ->assertRedirect(route('admin.guru'));
        $this->actingAs($admin)
            ->delete(route('admin.fasilitas.destroy', $fasilitas->id))
            ->assertRedirect(route('admin.fasilitas'));

        $this->assertDatabaseMissing('guru', ['id' => $guru->id]);
        $this->assertDatabaseMissing('fasilitas', ['id' => $fasilitas->id]);
    }

    private function adminUser(): User
    {
        return User::create([
            'username' => 'admin-content-test',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);
    }
}
