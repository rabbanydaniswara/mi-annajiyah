<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_seeder_can_create_default_admin_for_development(): void
    {
        $this->app['env'] = 'local';

        $this->runDatabaseSeeder();

        $admin = User::where('username', 'admin')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('admin123', $admin->password));
    }

    public function test_production_seeder_does_not_create_default_admin_credentials(): void
    {
        $this->app['env'] = 'production';

        $this->runDatabaseSeeder();

        $this->assertDatabaseMissing('users', [
            'username' => 'admin',
        ]);
        $this->assertDatabaseCount('siswa', 0);
        $this->assertDatabaseCount('jadwal', 0);
    }

    private function runDatabaseSeeder(): void
    {
        $seeder = $this->app->make(DatabaseSeeder::class);
        $seeder->setContainer($this->app);
        $seeder->run();
    }
}
