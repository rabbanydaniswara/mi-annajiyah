<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\KegiatanKategori;
use App\Models\KegiatanSekolah;
use App\Models\KontenWeb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_pagination_uses_public_controls_and_preserves_filter(): void
    {
        $category = KegiatanKategori::create([
            'nama' => 'Kegiatan QA',
            'warna' => 'green',
        ]);

        foreach (range(1, 25) as $index) {
            KegiatanSekolah::create([
                'kategori_id' => $category->id,
                'judul' => "Kegiatan QA {$index}",
                'tanggal' => now()->subDays($index)->toDateString(),
            ]);
        }

        $this->get(route('kegiatan', ['kategori' => $category->id]))
            ->assertOk()
            ->assertSee('Menampilkan')
            ->assertSee('1-12')
            ->assertSee('dari')
            ->assertSee('25')
            ->assertSee('Halaman')
            ->assertSee('Ke halaman berikutnya')
            ->assertSee('aria-current="page"', false)
            ->assertSee("kategori={$category->id}&amp;page=2#daftar-kegiatan", false)
            ->assertDontSee('pagination.next')
            ->assertDontSee('pagination.previous');

        $this->get(route('kegiatan', [
            'kategori' => $category->id,
            'page' => 2,
        ]))
            ->assertOk()
            ->assertSee('13-24')
            ->assertSee('Ke halaman sebelumnya')
            ->assertSee("kategori={$category->id}&amp;page=1#daftar-kegiatan", false);
    }

    public function test_invalid_activity_category_falls_back_to_all_activities(): void
    {
        KegiatanSekolah::create([
            'judul' => 'Kegiatan Tetap Tampil',
            'tanggal' => now()->toDateString(),
        ]);

        $this->get(route('kegiatan', ['kategori' => 999999]))
            ->assertOk()
            ->assertSee('Kegiatan Tetap Tampil')
            ->assertSee('1-1')
            ->assertDontSee('kategori=999999');
    }

    public function test_public_activity_and_teacher_cards_expose_keyboard_controls_and_dialogs(): void
    {
        KegiatanSekolah::create([
            'judul' => 'Kegiatan Aksesibel',
            'tanggal' => now()->toDateString(),
        ]);

        Guru::create([
            'nama' => 'Guru Aksesibel',
            'mapel' => 'Matematika',
            'jabatan' => 'Guru',
            'tampilkan' => true,
        ]);

        $this->get(route('kegiatan'))
            ->assertOk()
            ->assertSee('role="button"', false)
            ->assertSee('tabindex="0"', false)
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false);

        $this->get(route('tenaga-pendidik'))
            ->assertOk()
            ->assertSee('Lihat profil Guru Aksesibel')
            ->assertSee('role="dialog"', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Lihat profil Guru Aksesibel')
            ->assertSee('home-guru-dialog-title');
    }

    public function test_homepage_ppdb_call_to_action_reflects_closed_status(): void
    {
        KontenWeb::create([
            'tipe' => 'ppdb_tahun_ajaran',
            'konten' => '2027/2028',
        ]);
        KontenWeb::create([
            'tipe' => 'ppdb_status',
            'konten' => 'closed',
        ]);
        KontenWeb::create([
            'tipe' => 'ppdb_pesan_tutup',
            'konten' => 'Pendaftaran online belum dibuka kembali.',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('PPDB 2027/2028 Sedang Ditutup')
            ->assertSee('Pendaftaran online belum dibuka kembali.')
            ->assertSee('Lihat Informasi PPDB');
    }
}
