<?php

namespace Tests\Feature;

use App\Helpers\ImageHelper;
use App\Helpers\PublicCacheHelper;
use App\Models\Fasilitas;
use App\Models\Guru;
use App\Models\KegiatanKategori;
use App\Models\KegiatanSekolah;
use App\Models\KontenWeb;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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
            ->assertRedirect(route('admin.konten', ['tab' => 'kegiatan']));

        $kegiatan = KegiatanSekolah::where('judul', 'Manasik Haji Cilik')->firstOrFail();
        $this->actingAs($admin)
            ->delete(route('admin.konten.destroyKegiatan', $kegiatan->id))
            ->assertRedirect(route('admin.konten', ['tab' => 'kegiatan']));

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

    public function test_admin_can_edit_activity_and_category(): void
    {
        $admin = $this->adminUser();
        $kategori = KegiatanKategori::create(['nama' => 'Kategori Lama', 'warna' => 'green']);
        $kegiatan = KegiatanSekolah::create([
            'judul' => 'Kegiatan Lama',
            'deskripsi' => 'Deskripsi lama',
            'tanggal' => '2026-05-20',
            'kategori_id' => $kategori->id,
            'gambar' => 'uploads/kegiatan/existing.webp',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.konten.storeKategori'), [
                'id' => $kategori->id,
                'nama' => 'Kategori Baru',
                'warna' => 'blue',
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'kegiatan']));

        $this->actingAs($admin)
            ->post(route('admin.konten.storeKegiatan'), [
                'id' => $kegiatan->id,
                'judul' => 'Kegiatan Diperbarui',
                'deskripsi' => 'Deskripsi baru',
                'tanggal' => '2026-06-01',
                'kategori_id' => $kategori->id,
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'kegiatan']));

        $kategori->refresh();
        $kegiatan->refresh();
        $this->assertSame('Kategori Baru', $kategori->nama);
        $this->assertSame('blue', $kategori->warna);
        $this->assertSame('Kegiatan Diperbarui', $kegiatan->judul);
        $this->assertSame('uploads/kegiatan/existing.webp', $kegiatan->gambar);

        $this->actingAs($admin)
            ->get(route('admin.konten', ['tab' => 'kegiatan', 'edit_kegiatan' => $kegiatan->id]))
            ->assertOk()
            ->assertSee('Edit Kegiatan')
            ->assertSee('Kegiatan Diperbarui');
    }

    public function test_admin_content_rejects_unknown_content_type(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->from(route('admin.konten'))
            ->post(route('admin.konten.update'), [
                'tipe' => 'unknown_content',
                'konten' => 'Tidak boleh tersimpan.',
            ])
            ->assertRedirect(route('admin.konten'))
            ->assertSessionHasErrors('tipe');

        $this->assertDatabaseMissing('konten_web', [
            'tipe' => 'unknown_content',
        ]);
    }

    public function test_admin_contact_content_validates_email_and_allowed_keys(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->from(route('admin.konten', ['tab' => 'kontak']))
            ->post(route('admin.konten.update'), [
                'tipe' => 'kontak_multi',
                'konten_items' => [
                    'alamat' => 'Jl. Pendidikan',
                    'email' => 'email-tidak-valid',
                    'script' => 'nilai tidak dikenal',
                ],
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'kontak']))
            ->assertSessionHasErrors(['konten_items.email', 'konten_items']);
    }

    public function test_admin_can_update_all_contact_content_and_clear_public_cache(): void
    {
        $admin = $this->adminUser();
        Cache::put(PublicCacheHelper::KONTEN_WEB, ['alamat' => 'Nilai lama'], 3600);

        $contacts = [
            'alamat' => 'Jl. Pendidikan No. 80',
            'telepon' => '+62 21 1234 5678',
            'email' => 'kontak@miannajiyah.site',
            'wa' => '081234567890',
            'ig' => 'https://www.instagram.com/mi_annajiyah',
            'tiktok' => 'https://www.tiktok.com/@mis.annajiyah',
            'jam_op' => 'Senin - Jumat: 07.00 - 13.30 WIB',
        ];

        $this->actingAs($admin)
            ->post(route('admin.konten.update'), [
                'tipe' => 'kontak_multi',
                'konten_items' => $contacts,
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'kontak']))
            ->assertSessionHas('success');

        foreach ($contacts as $type => $value) {
            $this->assertDatabaseHas('konten_web', [
                'tipe' => $type,
                'konten' => $value,
            ]);
        }

        $this->assertFalse(Cache::has(PublicCacheHelper::KONTEN_WEB));
    }

    public function test_contact_validation_keeps_input_and_shows_field_error(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)
            ->from(route('admin.konten', ['tab' => 'kontak']))
            ->post(route('admin.konten.update'), [
                'tipe' => 'kontak_multi',
                'konten_items' => [
                    'alamat' => 'Alamat yang sedang diedit',
                    'telepon' => '',
                    'email' => 'tidak-valid',
                    'wa' => '123',
                    'ig' => '',
                    'tiktok' => '',
                    'jam_op' => '',
                ],
            ]);

        $response
            ->assertRedirect(route('admin.konten', ['tab' => 'kontak']))
            ->assertSessionHasErrors(['konten_items.email', 'konten_items.wa'])
            ->assertSessionHasInput('konten_items.alamat', 'Alamat yang sedang diedit');

        $followed = $this->followRedirects($response);
        $followed
            ->assertOk()
            ->assertSee('Kontak belum tersimpan')
            ->assertSee('Alamat yang sedang diedit');
    }

    public function test_contact_update_is_atomic_when_validation_fails(): void
    {
        $admin = $this->adminUser();
        KontenWeb::create(['tipe' => 'alamat', 'konten' => 'Alamat awal']);

        $this->actingAs($admin)
            ->post(route('admin.konten.update'), [
                'tipe' => 'kontak_multi',
                'konten_items' => [
                    'alamat' => 'Alamat baru',
                    'telepon' => '',
                    'email' => 'email-rusak',
                    'wa' => '',
                    'ig' => '',
                    'tiktok' => '',
                    'jam_op' => '',
                ],
            ])
            ->assertSessionHasErrors('konten_items.email');

        $this->assertDatabaseHas('konten_web', [
            'tipe' => 'alamat',
            'konten' => 'Alamat awal',
        ]);
    }

    public function test_banner_update_requires_existing_banner_id(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->from(route('admin.konten', ['tab' => 'banner']))
            ->put(route('admin.konten.updateBanner'), [
                'banner_id' => 999,
                'judul_banner' => 'Banner Test',
                'urutan_banner' => 1,
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'banner']))
            ->assertSessionHasErrors('banner_id');
    }

    public function test_image_helper_deletes_uploaded_image_variants(): void
    {
        $directory = public_path('uploads/test-media');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $files = [
            $directory.'/sample.webp',
            $directory.'/sample_thumb.webp',
            $directory.'/sample_card.webp',
            $directory.'/sample_hero.webp',
        ];

        foreach ($files as $file) {
            file_put_contents($file, 'test');
        }

        try {
            ImageHelper::deleteImageSet('uploads/test-media/sample.webp');

            foreach ($files as $file) {
                $this->assertFileDoesNotExist($file);
            }
        } finally {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            @rmdir($directory);
        }
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
