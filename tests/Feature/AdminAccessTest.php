<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CvSeeder::class);
    }

    public function test_registration_route_does_not_exist(): void
    {
        $this->get('/register')->assertNotFound();
        $this->postJson('/api/register')->assertNotFound();
    }

    public function test_only_one_user_is_seeded(): void
    {
        $this->assertSame(1, User::count());
    }

    public function test_guests_are_redirected_from_the_admin_shell_to_the_login(): void
    {
        foreach (['/admin', '/admin/experience', '/admin/projects'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }

        $this->get('/admin/login')->assertOk()->assertSee('<div id="app"', false);
    }

    public function test_guests_get_401_from_the_admin_api(): void
    {
        foreach (['/api/user', '/api/admin/dashboard', '/api/admin/experiences', '/api/admin/projects'] as $url) {
            $this->getJson($url)->assertUnauthorized();
        }
    }

    public function test_the_seeded_user_can_reach_the_admin(): void
    {
        $user = User::firstOrFail();

        $this->actingAs($user)->get('/admin')->assertOk();

        $this->actingAs($user)
            ->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('data.email', 'johanherreraramirez@outlook.com');
    }

    public function test_the_spa_logs_in_with_the_session_cookie(): void
    {
        // Sanctum only treats requests from the app's own origin as stateful.
        $this->withHeader('Referer', url('/'));

        $this->postJson('/api/login', [
            'email' => 'johanherreraramirez@outlook.com',
            'password' => 'test-password',
        ])->assertOk()->assertJsonPath('data.email', 'johanherreraramirez@outlook.com');

        $this->assertAuthenticatedAs(User::firstOrFail(), 'web');

        $this->getJson('/api/user')->assertOk();

        $this->postJson('/api/logout')->assertNoContent();
        $this->assertGuest('web');
    }

    public function test_a_wrong_password_is_a_validation_error(): void
    {
        $this->withHeader('Referer', url('/'))
            ->postJson('/api/login', [
                'email' => 'johanherreraramirez@outlook.com',
                'password' => 'wrong',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertGuest('web');
    }

    public function test_the_seeded_password_comes_from_the_environment(): void
    {
        $this->assertTrue(
            auth()->attempt([
                'email' => 'johanherreraramirez@outlook.com',
                'password' => 'test-password',
            ])
        );
    }
}
