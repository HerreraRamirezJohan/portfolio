<?php

namespace Tests\Feature;

use Database\Seeders\CvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CvSeeder::class);
    }

    public function test_root_redirects_to_the_default_locale(): void
    {
        $this->get('/')->assertRedirect('/'.config('app.locale'));
    }

    public function test_spanish_page_renders_the_cv(): void
    {
        $this->get('/es')
            ->assertOk()
            ->assertSee('Johan Osvaldo Herrera Ramírez')
            ->assertSee('Ingeniero Informático')
            ->assertSee('MAVI de Occidente')
            ->assertSee('Universidad de Guadalajara (CUCEI)')
            ->assertSee('Ingeniería en Computación')
            ->assertDontSee('DocChain');
    }

    public function test_english_page_renders_the_english_translations(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('Computer Engineer')
            ->assertSee('Backend Developer, SAP and Enterprise Systems')
            ->assertSee('Bachelor of Engineering in Computer Science')
            ->assertDontSee('Ingeniero Informático');
    }

    public function test_bullets_are_translated_per_locale(): void
    {
        $this->get('/es')->assertSee('Análisis de requerimientos');
        $this->get('/en')->assertSee('Requirements analysis');
    }

    public function test_an_unknown_locale_is_not_routable(): void
    {
        $this->get('/fr')->assertNotFound();
    }
}
