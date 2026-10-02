<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\User;
use Database\Seeders\CvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CvSeeder::class);
    }

    public function test_the_shell_inlines_the_payload_of_its_own_page(): void
    {
        $this->get('/es')->assertSee('id="bootstrap" data-url="/es/portfolio"', false);
        $this->get('/en/sap')->assertSee('id="bootstrap" data-url="/en/paths/sap"', false);
    }

    public function test_the_inlined_payload_cannot_close_its_script_tag(): void
    {
        $user = User::firstOrFail();
        $user->update(['name' => 'Johan </script><b>']);

        $html = $this->get('/es')->getContent();

        $this->assertStringNotContainsString('</script><b>', $html);
    }

    public function test_public_endpoints_answer_304_to_a_matching_etag(): void
    {
        $first = $this->getJson('/api/en/portfolio')->assertOk();
        $etag = $first->headers->get('ETag');

        $this->assertNotNull($etag);
        $this->assertStringContainsString('must-revalidate', $first->headers->get('Cache-Control'));

        $this->getJson('/api/en/portfolio', ['If-None-Match' => $etag])->assertStatus(304);
    }

    public function test_public_endpoints_never_start_a_session(): void
    {
        // The Referer is what makes Sanctum treat an API request as stateful.
        $response = $this->getJson('/api/en/portfolio', ['Referer' => url('/')])->assertOk();

        $this->assertSame([], $response->headers->getCookies());
    }

    public function test_the_payload_is_served_from_the_cache(): void
    {
        $this->getJson('/api/en/portfolio')->assertJsonCount(2, 'data.languages');

        Language::query()->delete();

        $this->getJson('/api/en/portfolio')->assertJsonCount(2, 'data.languages');
    }

    public function test_an_admin_write_clears_the_cache_for_every_page(): void
    {
        $this->getJson('/api/en/portfolio')->assertJsonCount(2, 'data.languages');
        $this->getJson('/api/es/portfolio')->assertJsonCount(2, 'data.languages');

        $this->actingAs(User::firstOrFail())
            ->deleteJson('/api/admin/languages/'.Language::firstOrFail()->id)
            ->assertSuccessful();

        $this->getJson('/api/en/portfolio')->assertJsonCount(1, 'data.languages');
        $this->getJson('/api/es/portfolio')->assertJsonCount(1, 'data.languages');
    }

    public function test_the_cache_can_be_switched_off(): void
    {
        config(['portfolio.cache_ttl' => 0]);

        $this->getJson('/api/en/portfolio')->assertJsonCount(2, 'data.languages');

        Language::query()->delete();

        $this->getJson('/api/en/portfolio')->assertJsonCount(0, 'data.languages');
    }
}
