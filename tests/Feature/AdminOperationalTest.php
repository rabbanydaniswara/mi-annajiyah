<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminOperationalTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_change_own_password(): void
    {
        $operator = User::create([
            'username' => 'operator-password',
            'password' => 'old-secret',
            'role' => 'operator',
        ]);

        $this->actingAs($operator)
            ->put(route('admin.password.update'), [
                'current_password' => 'old-secret',
                'password' => 'new-secret-123',
                'password_confirmation' => 'new-secret-123',
            ])
            ->assertRedirect(route('admin.password.edit'));

        $this->assertTrue(Hash::check('new-secret-123', $operator->fresh()->password));
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $operator->id,
            'action' => 'change_password',
        ]);
    }

    public function test_change_password_rejects_wrong_current_password(): void
    {
        $admin = User::create([
            'username' => 'admin-password',
            'password' => 'old-secret',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.password.edit'))
            ->put(route('admin.password.update'), [
                'current_password' => 'wrong-secret',
                'password' => 'new-secret-123',
                'password_confirmation' => 'new-secret-123',
            ])
            ->assertRedirect(route('admin.password.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-secret', $admin->fresh()->password));
    }

    public function test_admin_dashboard_shows_ppdb_workflow_summary(): void
    {
        $admin = User::create([
            'username' => 'admin-dashboard-summary',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);
        Siswa::create(['nama' => 'Pendaftar Pending', 'status_ppdb' => 'pending']);
        Siswa::create(['nama' => 'Pendaftar Diterima', 'status_ppdb' => 'diterima']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan Verifikasi PPDB')
            ->assertSee('Pendaftar Pending')
            ->assertSee('Diterima');
    }

    public function test_admin_activity_log_can_be_filtered(): void
    {
        $admin = User::create([
            'username' => 'admin-log-filter',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'update_status',
            'model_type' => 'Siswa',
            'description' => 'Log yang dicari',
            'ip_address' => '127.0.0.1',
        ]);
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'delete_siswa',
            'model_type' => 'Siswa',
            'description' => 'Log yang tidak dicari',
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.admin', [
                'tab' => 'logs',
                'log_action' => 'update_status',
                'log_model' => 'Siswa',
            ]))
            ->assertOk()
            ->assertSee('Log yang dicari')
            ->assertDontSee('Log yang tidak dicari');
    }
}
