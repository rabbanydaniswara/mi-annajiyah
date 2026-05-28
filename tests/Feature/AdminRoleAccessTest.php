<?php

namespace Tests\Feature;

use App\Models\KontenWeb;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_user_management(): void
    {
        $admin = User::create([
            'username' => 'admin-role-test',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.admin'))
            ->assertOk()
            ->assertSee('Manajemen Admin');
    }

    public function test_operator_cannot_access_admin_user_management(): void
    {
        $operator = User::create([
            'username' => 'operator-role-test',
            'password' => 'secret-password',
            'role' => 'operator',
        ]);

        $this->actingAs($operator)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($operator)
            ->get(route('admin.admin'))
            ->assertForbidden();
    }

    public function test_admin_can_update_active_ppdb_academic_year(): void
    {
        $admin = User::create([
            'username' => 'admin-ppdb-setting',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.konten.update'), [
                'tipe' => 'ppdb_settings',
                'tahun_ajaran' => '2028-2029',
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'ppdb']));

        $this->assertSame(
            '2028/2029',
            KontenWeb::where('tipe', 'ppdb_tahun_ajaran')->value('konten')
        );
    }

    public function test_admin_ppdb_page_shows_registration_number_and_academic_year_filter(): void
    {
        $admin = User::create([
            'username' => 'admin-ppdb-list',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Ahmad Daniswara',
            'nisn' => '0012345678',
            'nis' => 'NIS-001',
            'no_wa' => '081234567890',
            'tahun_ajaran' => '2028/2029',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.ppdb', ['tahun_ajaran' => '2028/2029']))
            ->assertOk()
            ->assertSee($siswa->nomor_pendaftaran)
            ->assertSee('2028/2029')
            ->assertSee('Ahmad Daniswara');
    }
}
