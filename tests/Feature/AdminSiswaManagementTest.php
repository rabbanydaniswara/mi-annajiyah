<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSiswaManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_siswa_rejects_duplicate_nisn_and_nis(): void
    {
        $admin = $this->adminUser();
        Siswa::create([
            'nama' => 'Siswa Lama',
            'nisn' => '0012345678',
            'nis' => 'NIS-001',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.siswa'))
            ->post(route('admin.siswa.store'), [
                'nama' => 'Siswa Baru',
                'nisn' => '0012345678',
                'nis' => 'NIS-001',
            ])
            ->assertRedirect(route('admin.siswa'))
            ->assertSessionHasErrors(['nisn', 'nis']);

        $this->assertSame(1, Siswa::where('nisn', '0012345678')->count());
    }

    public function test_admin_siswa_allows_updating_the_same_identifiers(): void
    {
        $admin = $this->adminUser();
        $siswa = Siswa::create([
            'nama' => 'Siswa Lama',
            'nisn' => '0012345678',
            'nis' => 'NIS-001',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.siswa.store'), [
                'id' => $siswa->id,
                'nama' => 'Siswa Diperbarui',
                'nisn' => '0012345678',
                'nis' => 'NIS-001',
            ])
            ->assertRedirect(route('admin.siswa'));

        $this->assertDatabaseHas('siswa', [
            'id' => $siswa->id,
            'nama' => 'Siswa Diperbarui',
            'nisn' => '0012345678',
            'nis' => 'NIS-001',
        ]);
    }

    public function test_admin_siswa_rejects_invalid_identity_and_contact_formats(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->from(route('admin.siswa'))
            ->post(route('admin.siswa.store'), [
                'nama' => 'Siswa 123',
                'nisn' => '123456789',
                'nis' => 'NIS@001',
                'kelas' => '1A@',
                'no_wa' => '12345',
                'nama_ortu' => 'Orang Tua 123',
                'alamat' => 'Pendek',
            ])
            ->assertRedirect(route('admin.siswa'))
            ->assertSessionHasErrors([
                'nama',
                'nisn',
                'nis',
                'kelas',
                'no_wa',
                'nama_ortu',
                'alamat',
            ]);

        $this->assertDatabaseCount('siswa', 0);
    }

    public function test_admin_siswa_normalizes_valid_text_fields(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.siswa.store'), [
                'nama' => "  Siti   Nur 'Aini ",
                'nisn' => '0012345678',
                'nis' => 'NIS-001',
                'kelas' => ' 1A ',
                'no_wa' => '0812 3456 7890',
                'nama_ortu' => '  Muhammad   Al-Fatih ',
                'alamat' => " Jl. Pendidikan   No. 1\n RT 01 / RW 02 ",
            ])
            ->assertRedirect(route('admin.siswa'));

        $this->assertDatabaseHas('siswa', [
            'nama' => "Siti Nur 'Aini",
            'nisn' => '0012345678',
            'nis' => 'NIS-001',
            'kelas' => '1A',
            'no_wa' => '081234567890',
            'nama_ortu' => 'Muhammad Al-Fatih',
            'alamat' => "Jl. Pendidikan No. 1\nRT 01 / RW 02",
        ]);
    }

    private function adminUser(): User
    {
        return User::create([
            'username' => 'admin-siswa-test',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);
    }
}
