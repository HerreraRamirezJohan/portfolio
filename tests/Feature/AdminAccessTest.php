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

    public function test_guests_are_redirected_from_the_admin(): void
    {
        foreach (['/admin', '/admin/experience', '/admin/projects'] as $url) {
            $this->get($url)->assertRedirect();
        }
    }

    public function test_the_seeded_user_can_reach_the_admin(): void
    {
        $this->actingAs(User::firstOrFail())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard');
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
