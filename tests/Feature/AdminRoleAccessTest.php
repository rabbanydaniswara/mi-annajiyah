<?php

namespace Tests\Feature;

use App\Models\KontenWeb;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_admin_user_password_must_be_confirmed_and_at_least_eight_characters(): void
    {
        $admin = User::create([
            'username' => 'admin-password-policy',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.admin'))
            ->post(route('admin.admin.store'), [
                'username' => 'new-operator-short',
                'role' => 'operator',
                'password' => 'short',
                'password_confirmation' => 'short',
            ])
            ->assertRedirect(route('admin.admin'))
            ->assertSessionHasErrors('password');

        $this->actingAs($admin)
            ->from(route('admin.admin'))
            ->post(route('admin.admin.store'), [
                'username' => 'new-operator-mismatch',
                'role' => 'operator',
                'password' => 'strong-password',
                'password_confirmation' => 'different-password',
            ])
            ->assertRedirect(route('admin.admin'))
            ->assertSessionHasErrors('password');
    }

    public function test_admin_can_create_operator_with_strong_confirmed_password(): void
    {
        $admin = User::create([
            'username' => 'admin-create-operator',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.admin.store'), [
                'username' => 'operator-created',
                'role' => 'operator',
                'password' => 'strong-password',
                'password_confirmation' => 'strong-password',
            ])
            ->assertRedirect(route('admin.admin'));

        $operator = User::where('username', 'operator-created')->first();

        $this->assertNotNull($operator);
        $this->assertSame('operator', $operator->role);
        $this->assertTrue(Hash::check('strong-password', $operator->password));
    }

    public function test_admin_cannot_delete_current_account(): void
    {
        $admin = User::create([
            'username' => 'admin-current-delete',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.admin.destroy', $admin->id))
            ->assertRedirect(route('admin.admin'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'username' => 'admin-current-delete',
        ]);
    }

    public function test_admin_cannot_demote_last_admin_to_operator(): void
    {
        $admin = User::create([
            'username' => 'admin-last-role',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.admin', ['edit' => $admin->id]))
            ->post(route('admin.admin.store'), [
                'id' => $admin->id,
                'username' => 'admin-last-role',
                'role' => 'operator',
            ])
            ->assertRedirect(route('admin.admin', ['edit' => $admin->id]))
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
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
                'status_pendaftaran' => 'closed',
                'pesan_tutup' => 'Pendaftaran gelombang ini telah berakhir.',
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'ppdb']));

        $this->assertSame(
            '2028/2029',
            KontenWeb::where('tipe', 'ppdb_tahun_ajaran')->value('konten')
        );
        $this->assertSame('closed', KontenWeb::where('tipe', 'ppdb_status')->value('konten'));
        $this->assertSame(
            'Pendaftaran gelombang ini telah berakhir.',
            KontenWeb::where('tipe', 'ppdb_pesan_tutup')->value('konten')
        );
    }

    public function test_admin_must_set_public_message_when_closing_ppdb(): void
    {
        $admin = User::create([
            'username' => 'admin-ppdb-message',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.konten', ['tab' => 'ppdb']))
            ->post(route('admin.konten.update'), [
                'tipe' => 'ppdb_settings',
                'tahun_ajaran' => '2028/2029',
                'status_pendaftaran' => 'closed',
                'pesan_tutup' => '',
            ])
            ->assertRedirect(route('admin.konten', ['tab' => 'ppdb']))
            ->assertSessionHasErrors('pesan_tutup');

        $this->assertDatabaseMissing('konten_web', [
            'tipe' => 'ppdb_status',
            'konten' => 'closed',
        ]);
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
            ->assertSee('Ahmad Daniswara')
            ->assertDontSee('<th class="p-4 text-left">Dokumen</th>', false);
    }
}
