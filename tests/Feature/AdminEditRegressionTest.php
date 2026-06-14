<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Fasilitas;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminEditRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_guru_can_be_edited_without_replacing_photo(): void
    {
        $admin = $this->adminUser();
        $guru = Guru::create([
            'nama' => 'Guru Lama',
            'mapel' => 'Matematika',
            'jabatan' => 'Guru',
            'foto' => 'uploads/guru/existing.webp',
            'urutan' => 1,
            'tampilkan' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.guru.store'), [
                'id' => $guru->id,
                'nama' => 'Guru Diperbarui',
                'mapel' => 'Bahasa Indonesia',
                'jabatan' => 'Wali Kelas',
                'urutan' => 2,
                'tampilkan' => '1',
            ])
            ->assertRedirect(route('admin.guru'))
            ->assertSessionHas('success');

        $guru->refresh();
        $this->assertSame('Guru Diperbarui', $guru->nama);
        $this->assertSame('Bahasa Indonesia', $guru->mapel);
        $this->assertSame('uploads/guru/existing.webp', $guru->foto);
        $this->assertTrue($guru->tampilkan);
    }

    public function test_existing_fasilitas_can_be_edited_without_replacing_image(): void
    {
        $admin = $this->adminUser();
        $fasilitas = Fasilitas::create([
            'nama' => 'Fasilitas Lama',
            'deskripsi' => 'Deskripsi lama',
            'ikon' => 'fas fa-school',
            'gambar' => 'uploads/fasilitas/existing.webp',
            'urutan' => 1,
            'aktif' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.fasilitas.store'), [
                'id' => $fasilitas->id,
                'nama' => 'Fasilitas Diperbarui',
                'deskripsi' => 'Deskripsi baru',
                'ikon' => 'fas fa-book',
                'urutan' => 3,
            ])
            ->assertRedirect(route('admin.fasilitas'))
            ->assertSessionHas('success');

        $fasilitas->refresh();
        $this->assertSame('Fasilitas Diperbarui', $fasilitas->nama);
        $this->assertSame('uploads/fasilitas/existing.webp', $fasilitas->gambar);
        $this->assertFalse($fasilitas->aktif);
    }

    public function test_existing_jadwal_can_be_edited_without_self_conflict(): void
    {
        $admin = $this->adminUser();
        $guru = Guru::create(['nama' => 'Guru Jadwal', 'mapel' => 'Umum']);
        $jadwal = Jadwal::create([
            'hari' => 'Senin',
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:00',
            'mapel' => 'Matematika',
            'id_guru' => $guru->id,
            'kelas' => '1A',
            'ruangan' => 'Ruang 1A',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.jadwal.store'), [
                'id' => $jadwal->id,
                'hari' => 'Selasa',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:00',
                'mapel' => 'Bahasa Indonesia',
                'id_guru' => $guru->id,
                'kelas' => '1A',
                'ruangan' => 'Ruang 1A',
            ])
            ->assertRedirect(route('admin.jadwal'))
            ->assertSessionHas('success');

        $jadwal->refresh();
        $this->assertSame('Selasa', $jadwal->hari);
        $this->assertSame('Bahasa Indonesia', $jadwal->mapel);
    }

    public function test_existing_siswa_can_be_edited(): void
    {
        $admin = $this->adminUser();
        $siswa = Siswa::create([
            'nama' => 'Siswa Lama',
            'nisn' => '0012345678',
            'nis' => 'NIS-001',
            'kelas' => '1A',
            'no_wa' => '081234567890',
            'nama_ortu' => 'Orang Tua Lama',
            'alamat' => 'Alamat lama yang lengkap',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.siswa.store'), [
                'id' => $siswa->id,
                'nama' => 'Siswa Diperbarui',
                'nisn' => '0012345678',
                'nis' => 'NIS-001',
                'kelas' => '2A',
                'no_wa' => '081298765432',
                'nama_ortu' => 'Orang Tua Baru',
                'alamat' => 'Alamat baru yang lengkap',
            ])
            ->assertRedirect(route('admin.siswa'))
            ->assertSessionHas('success');

        $siswa->refresh();
        $this->assertSame('Siswa Diperbarui', $siswa->nama);
        $this->assertSame('2A', $siswa->kelas);
        $this->assertSame('081298765432', $siswa->no_wa);
    }

    public function test_existing_banner_can_be_edited_without_replacing_image(): void
    {
        $admin = $this->adminUser();
        $banner = Banner::create([
            'judul' => 'Banner Lama',
            'subtitle' => 'Subtitle lama',
            'gambar' => 'uploads/banner/existing.webp',
            'urutan' => 1,
            'aktif' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.konten.updateBanner'), [
                'banner_id' => $banner->id,
                'judul_banner' => 'Banner Diperbarui',
                'subtitle_banner' => 'Subtitle baru',
                'urutan_banner' => 4,
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'banner']))
            ->assertSessionHas('success');

        $banner->refresh();
        $this->assertSame('Banner Diperbarui', $banner->judul);
        $this->assertSame('uploads/banner/existing.webp', $banner->gambar);
        $this->assertSame(4, $banner->urutan);
    }

    public function test_existing_operator_can_be_edited_and_password_is_optional(): void
    {
        $admin = $this->adminUser();
        $operator = User::create([
            'username' => 'operator-lama',
            'password' => 'password-lama',
            'role' => 'operator',
        ]);
        $oldHash = $operator->password;

        $this->actingAs($admin)
            ->post(route('admin.admin.store'), [
                'id' => $operator->id,
                'username' => 'operator-baru',
                'role' => 'operator',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.admin'))
            ->assertSessionHas('success');

        $operator->refresh();
        $this->assertSame('operator-baru', $operator->username);
        $this->assertSame($oldHash, $operator->password);

        $this->actingAs($admin)
            ->post(route('admin.admin.store'), [
                'id' => $operator->id,
                'username' => 'operator-baru',
                'role' => 'operator',
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])
            ->assertRedirect(route('admin.admin'));

        $this->assertTrue(Hash::check('password-baru', $operator->fresh()->password));
    }

    public function test_edit_forms_load_existing_values(): void
    {
        $admin = $this->adminUser();
        $guru = Guru::create(['nama' => 'Guru Form Edit', 'mapel' => 'IPA']);
        $fasilitas = Fasilitas::create(['nama' => 'Fasilitas Form Edit']);
        $siswa = Siswa::create(['nama' => 'Siswa Form Edit']);

        $this->actingAs($admin)
            ->get(route('admin.guru', ['edit' => $guru->id]))
            ->assertOk()
            ->assertSee('Guru Form Edit');

        $this->actingAs($admin)
            ->get(route('admin.fasilitas', ['edit' => $fasilitas->id]))
            ->assertOk()
            ->assertSee('Fasilitas Form Edit');

        $this->actingAs($admin)
            ->get(route('admin.siswa', ['edit' => $siswa->id]))
            ->assertOk()
            ->assertSee('Siswa Form Edit');
    }

    private function adminUser(): User
    {
        return User::create([
            'username' => 'admin-edit-regression',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);
    }
}
