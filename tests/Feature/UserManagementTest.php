<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function details(): array
    {
        return ['name' => 'New Operator', 'email' => 'operator@example.com', 'role' => 'User',
            'password' => 'secure-password', 'password_confirmation' => 'secure-password'];
    }

    public function test_admin_can_create_a_user_who_can_login(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $this->post('/settings/users', $this->details())->assertRedirect(route('settings.users'));
        $user = User::where('email', 'operator@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secure-password', $user->password));
        $this->assertSame('User', $user->role);
        $this->get('/settings/users')->assertOk()->assertSee('operator@example.com');
        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'secure-password'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_input_and_duplicate_emails_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $this->actingAs($admin);
        $this->post('/settings/users', [])->assertSessionHasErrors(['name', 'email', 'role', 'password']);
        $this->post('/settings/users', array_merge($this->details(), ['email' => strtoupper($admin->email), 'role' => 'Owner', 'password_confirmation' => 'wrong']))
            ->assertSessionHasErrors(['email', 'role', 'password']);
        $this->assertSame(1, User::count());
    }

    public function test_guests_demo_visitors_and_regular_users_cannot_create_accounts(): void
    {
        $this->post('/settings/users', $this->details())->assertRedirect(route('login'));
        $this->post('/demo/settings/users', $this->details())->assertStatus(405);
        $this->assertSame(0, User::count());
        $this->actingAs(User::factory()->create(['role' => 'User']));
        $this->get('/settings/users')->assertForbidden();
        $this->post('/settings/users', $this->details())->assertForbidden();
        $this->assertSame(1, User::count());
    }
}
