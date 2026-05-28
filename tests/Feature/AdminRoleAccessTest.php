<?php

namespace Tests\Feature;

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
}
