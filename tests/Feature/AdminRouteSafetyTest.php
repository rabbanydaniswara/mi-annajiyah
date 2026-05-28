<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Fasilitas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRouteSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_banner_toggle_uses_patch_not_get(): void
    {
        $admin = User::create([
            'username' => 'admin-toggle-banner',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $banner = Banner::create([
            'judul' => 'Banner Test',
            'aktif' => true,
            'urutan' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.konten.toggleBanner', $banner))
            ->assertMethodNotAllowed();

        $this->assertTrue($banner->fresh()->aktif);

        $this->actingAs($admin)
            ->patch(route('admin.konten.toggleBanner', $banner))
            ->assertRedirect(route('admin.konten'));

        $this->assertFalse($banner->fresh()->aktif);
    }

    public function test_fasilitas_toggle_uses_patch_not_get(): void
    {
        $admin = User::create([
            'username' => 'admin-toggle-fasilitas',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $fasilitas = Fasilitas::create([
            'nama' => 'Fasilitas Test',
            'aktif' => true,
            'urutan' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.fasilitas.toggle', $fasilitas))
            ->assertMethodNotAllowed();

        $this->assertTrue($fasilitas->fresh()->aktif);

        $this->actingAs($admin)
            ->patch(route('admin.fasilitas.toggle', $fasilitas))
            ->assertRedirect(route('admin.fasilitas'));

        $this->assertFalse($fasilitas->fresh()->aktif);
    }
}
