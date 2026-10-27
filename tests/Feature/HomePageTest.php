<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\CvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CvSeeder::class);
    }

    private function fakeExchangeRate(float $mxn = 18.3): void
    {
        Http::fake([
            'open.er-api.com/*' => Http::response(['result' => 'success', 'base_code' => 'USD', 'rates' => ['USD' => 1, 'MXN' => $mxn]]),
        ]);
    }

    public function test_the_shell_renders_the_freelance_head_for_crawlers(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('<title>Johan Osvaldo Herrera Ramírez - Websites, systems and SAP changes for your business</title>', false)
            ->assertSee('hreflang="es" href="'.url('/es').'"', false)
            ->assertSee('id="bootstrap" data-url="/en/home"', false);

        $this->fakeExchangeRate();
        $this->get('/es')->assertSee('Sitios web, sistemas y cambios en SAP para tu negocio', false);
    }

    public function test_the_cv_keeps_its_own_page_and_title(): void
    {
        $this->get('/es/cv')
            ->assertOk()
            ->assertSee('Ingeniero de Software · Backend, APIs e Integraciones Empresariales', false)
            ->assertSee('id="bootstrap" data-url="/es/cv"', false);
    }

    public function test_the_home_payload_lists_the_services_in_the_active_locale(): void
    {
        $this->fakeExchangeRate();

        $this->getJson('/api/es/home')
            ->assertOk()
            ->assertJsonPath('data.services.*.slug', ['programacion-web', 'desarrollo-sap'])
            ->assertJsonPath('data.services.0.title', 'Programación web')
            ->assertJsonPath('data.services.1.title', 'Desarrollo SAP (ABAP)')
            ->assertJsonPath('data.services.1.path', 'sap')
            ->assertJsonPath('data.services.0.topics', ['Diseño UI/UX', 'Diseño móvil', 'Análisis SEO/GEO', 'APIs e integraciones']);

        $this->getJson('/api/en/home')
            ->assertJsonPath('data.services.0.title', 'Web programming')
            ->assertJsonPath('data.services.0.topics.2', 'SEO/GEO analysis');
    }

    public function test_the_english_page_shows_dollars_without_asking_the_exchange_service(): void
    {
        $this->getJson('/api/en/home')
            ->assertJsonPath('data.services.0.rate', ['amount' => 15, 'currency' => 'USD', 'converted' => false])
            ->assertJsonPath('data.services.1.rate', ['amount' => 20, 'currency' => 'USD', 'converted' => false]);

        Http::assertNothingSent();
    }

    public function test_the_spanish_page_shows_pesos_at_the_current_rate(): void
    {
        $this->fakeExchangeRate(18.3);

        $this->getJson('/api/es/home')
            ->assertJsonPath('data.services.0.rate', ['amount' => 275, 'currency' => 'MXN', 'converted' => true])
            ->assertJsonPath('data.services.1.rate', ['amount' => 365, 'currency' => 'MXN', 'converted' => true]);

        // Both services share one request.
        Http::assertSentCount(1);
    }

    public function test_the_spanish_page_still_loads_when_the_exchange_service_is_down(): void
    {
        Http::fake(['open.er-api.com/*' => Http::response('', 503)]);

        $this->getJson('/api/es/home')
            ->assertOk()
            ->assertJsonPath('data.services.0.rate.amount', 275)
            ->assertJsonPath('data.services.0.rate.currency', 'MXN');
    }

    public function test_a_service_without_a_rate_shows_none(): void
    {
        Service::where('slug', 'desarrollo-sap')->update(['rate_usd' => null]);

        $this->getJson('/api/en/home')->assertJsonPath('data.services.1.rate', null);
    }

    public function test_the_home_page_lists_the_cost_factors(): void
    {
        $this->getJson('/api/en/home')
            ->assertJsonCount(6, 'data.cost_factors')
            ->assertJsonPath('data.cost_factors.0.title', 'UI/UX design')
            ->assertJsonPath('data.cost_factors.2.title', 'SEO/GEO analysis');

        $this->getJson('/api/es/home')
            ->assertJsonPath('data.cost_factors.1.title', 'Diseño móvil')
            ->assertJsonPath('data.cost_factors.4.title', 'Documentación');
    }

    public function test_unpublished_services_stay_off_the_home_page(): void
    {
        Service::where('slug', 'desarrollo-sap')->update(['is_published' => false]);

        $this->getJson('/api/en/home')->assertJsonPath('data.services.*.slug', ['programacion-web']);
    }

    public function test_the_home_page_has_the_process_and_the_faq(): void
    {
        $this->fakeExchangeRate();

        $this->getJson('/api/en/home')
            ->assertOk()
            ->assertJsonCount(4, 'data.process')
            ->assertJsonPath('data.process.1.title', 'You get a written quote')
            ->assertJsonCount(5, 'data.faqs')
            ->assertJsonPath('data.faqs.0.question', 'How much does it cost?');

        $this->getJson('/api/es/home')
            ->assertJsonPath('data.process.1.title', 'Recibes una cotización por escrito')
            ->assertJsonPath('data.faqs.0.question', '¿Cuánto cuesta?');
    }

    public function test_the_home_page_shows_the_years_in_production(): void
    {
        $response = $this->getJson('/api/en/home')->assertJsonPath('data.experience.count', 4);

        $this->assertNotNull($response->json('data.experience.years'));
    }

    public function test_the_home_page_omits_what_belongs_to_the_cv_and_any_whatsapp_number(): void
    {
        $data = $this->getJson('/api/en/home')->json('data');

        foreach (['projects', 'paths', 'educations', 'skills', 'pipelines'] as $key) {
            $this->assertArrayNotHasKey($key, $data);
        }

        $this->assertArrayNotHasKey('whatsapp', $data['person']);
    }

    public function test_the_old_portfolio_urls_are_gone(): void
    {
        $this->getJson('/api/en/portfolio')->assertNotFound();
        $this->get('/en/portfolio')->assertNotFound();
    }
}
