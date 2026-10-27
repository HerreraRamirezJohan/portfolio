<?php

namespace Tests\Feature;

use App\Support\HourlyRates;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HourlyRatesTest extends TestCase
{
    private function fakeRate(float $mxn): void
    {
        Http::fake([
            'open.er-api.com/*' => Http::response(['result' => 'success', 'base_code' => 'USD', 'rates' => ['MXN' => $mxn]]),
        ]);
    }

    private function rates(): HourlyRates
    {
        return app(HourlyRates::class);
    }

    public function test_dollars_pass_through_without_a_request(): void
    {
        $this->assertSame(['amount' => 15, 'currency' => 'USD', 'converted' => false], $this->rates()->forLocale(15, 'en'));

        Http::assertNothingSent();
    }

    public function test_a_locale_with_pesos_converts_at_the_current_rate(): void
    {
        $this->fakeRate(18.3);

        $this->assertSame(['amount' => 275, 'currency' => 'MXN', 'converted' => true], $this->rates()->forLocale(15, 'es'));
        $this->assertSame(['amount' => 365, 'currency' => 'MXN', 'converted' => true], $this->rates()->forLocale(20, 'es'));
    }

    public function test_the_amount_is_rounded_to_the_configured_step(): void
    {
        $this->fakeRate(18.3);
        config(['portfolio.rate_rounding' => 10]);

        // 15 x 18.3 = 274.5 -> 270; 20 x 18.3 = 366 -> 370
        $this->assertSame(270, $this->rates()->forLocale(15, 'es')['amount']);
        $this->assertSame(370, $this->rates()->forLocale(20, 'es')['amount']);
    }

    public function test_the_rate_follows_the_exchange_rate(): void
    {
        $this->fakeRate(20.0);

        $this->assertSame(300, $this->rates()->forLocale(15, 'es')['amount']);
    }

    public function test_a_locale_with_no_currency_shows_dollars(): void
    {
        $this->assertSame('USD', $this->rates()->forLocale(15, 'fr')['currency']);

        Http::assertNothingSent();
    }
}
