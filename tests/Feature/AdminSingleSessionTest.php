<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureSingleActiveAdminSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminSingleSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_stores_current_session_id(): void
    {
        $user = User::create([
            'username' => 'admin-single-session',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        $this->post(route('admin.login'), [
            'username' => 'admin-single-session',
            'password' => 'secret-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertNotNull($user->fresh()->active_session_id);
    }

    public function test_stale_admin_session_is_redirected_to_login(): void
    {
        $user = User::create([
            'username' => 'admin-stale-session',
            'password' => 'secret-password',
            'role' => 'admin',
            'active_session_id' => 'new-session-id',
        ]);

        Auth::login($user);

        $request = $this->requestWithSession('old-session-id');
        $request->setUserResolver(fn () => $user);

        $response = app(EnsureSingleActiveAdminSession::class)
            ->handle($request, fn () => response('ok'));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringContainsString('/admin/login', $response->headers->get('Location'));
        $this->assertGuest();
    }

    public function test_current_admin_session_is_allowed(): void
    {
        $request = $this->requestWithSession('current-session-id');

        $user = User::create([
            'username' => 'admin-current-session',
            'password' => 'secret-password',
            'role' => 'admin',
            'active_session_id' => $request->session()->getId(),
        ]);

        Auth::login($user);
        $request->setUserResolver(fn () => $user);

        $response = app(EnsureSingleActiveAdminSession::class)
            ->handle($request, fn () => response('ok'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertAuthenticatedAs($user);
    }

    private function requestWithSession(string $sessionId): Request
    {
        $request = Request::create('/admin', 'GET');
        $session = new Store('laravel_session', new ArraySessionHandler(120));
        $session->setId($sessionId);
        $session->start();
        $request->setLaravelSession($session);

        return $request;
    }
}
