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
    }

    public function test_only_one_user_is_seeded(): void
    {
        $this->assertSame(1, User::count());
    }

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        foreach (['/admin', '/admin/experience', '/admin/projects'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }
    }

    public function test_the_seeded_user_can_reach_the_admin(): void
    {
        // Asserts on the email rather than a heading, so the test does not
        // depend on which locale the panel happens to render in.
        $this->actingAs(User::firstOrFail())
            ->get('/admin')
            ->assertOk()
            ->assertSee('johanherreraramirez@outlook.com');
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
