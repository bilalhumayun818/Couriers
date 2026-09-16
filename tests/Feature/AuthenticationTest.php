<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_main_routes_require_authentication_and_demo_remains_public(): void
    {
        foreach (Route::getRoutes() as $route) {
            if (! $route->getName() || str_starts_with($route->getName(), 'demo.') || str_starts_with($route->getName(), 'login')) {
                continue;
            }
            if (in_array('web', $route->gatherMiddleware())) {
                $this->assertContains('auth', $route->gatherMiddleware(), $route->uri());
            }
        }
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->post('/operations/trips', [])->assertRedirect(route('login'));
        $this->get('/demo/dashboard')->assertOk();
        $this->get('/login')->assertOk()->assertSee('Sign in to your account');
    }

    public function test_admin_can_login_and_logout_and_password_is_hashed(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->assertTrue(Hash::check('12345678', User::first()->password));
        $this->post('/login', ['email' => 'admin@gail.com', 'password' => '12345678'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->get('/dashboard')->assertOk()->assertSee('Sign out');
        $this->get('/login')->assertRedirect(route('dashboard'));
        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_invalid_credentials_are_rejected_and_rate_limited(): void
    {
        $this->freezeTime();
        $this->seed(AdminUserSeeder::class);
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'admin@gail.com', 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => 'admin@gail.com', 'password' => '12345678'])
            ->assertSessionHasErrors(['email' => 'Too many attempts. Try again in 60 seconds.']);
        $this->assertGuest();
        $this->travel(61)->seconds();
        $this->post('/login', ['email' => 'admin@gail.com', 'password' => '12345678'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_sessions_last_thirty_days_and_refresh_on_activity(): void
    {
        $this->actingAs(User::factory()->create());
        $first = $this->get('/dashboard')->assertOk();
        $cookie = collect($first->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));
        $this->assertEqualsWithDelta(now()->addDays(30)->timestamp, $cookie->getExpiresTime(), 2);
        $this->travel(29)->days();
        $next = $this->get('/dashboard')->assertOk();
        $renewed = collect($next->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));
        $this->assertEqualsWithDelta(now()->addDays(30)->timestamp, $renewed->getExpiresTime(), 2);

        $handler = new DatabaseSessionHandler(DB::connection(), 'sessions', 43200, $this->app);
        $handler->write('expiry-test', 'session-payload');
        $this->travel(29)->days();
        $this->assertSame('session-payload', $handler->read('expiry-test'));
        $handler->write('expiry-test', 'renewed-payload');
        $this->travel(29)->days();
        $this->assertSame('renewed-payload', $handler->read('expiry-test'));
        $this->travel(2)->days();
        $this->assertSame('', $handler->read('expiry-test'));
    }

    public function test_admin_seeder_does_not_reset_an_existing_password(): void
    {
        $user = User::factory()->create(['email' => 'admin@gail.com', 'password' => 'changed-password']);
        $this->seed(AdminUserSeeder::class);
        $this->assertTrue(Hash::check('changed-password', $user->fresh()->password));
    }

    public function test_login_urls_respect_the_deployment_subdirectory(): void
    {
        config(['app.url' => 'https://example.com/courier']);
        $this->assertStringEndsWith('/courier/login', route('login'));
        $this->assertSame('/courier/login', route('login', [], false));
    }
}
