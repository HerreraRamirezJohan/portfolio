<?php

namespace Tests\Feature;

use App\Support\ExchangeRate;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    private const URL = 'open.er-api.com/*';

    private function success(float $mxn = 18.5): array
    {
        return ['result' => 'success', 'base_code' => 'USD', 'rates' => ['USD' => 1, 'MXN' => $mxn, 'EUR' => 0.9]];
    }

    private function rates(): ExchangeRate
    {
        return app(ExchangeRate::class);
    }

    public function test_it_reads_the_rate_from_the_api(): void
    {
        Http::fake([self::URL => Http::response($this->success(18.5))]);

        $this->assertSame(18.5, $this->rates()->perUsd('MXN'));
        $this->assertSame(18.5, $this->rates()->perUsd('mxn'));
    }

    public function test_dollars_need_no_request(): void
    {
        $this->assertSame(1.0, $this->rates()->perUsd('USD'));

        Http::assertNothingSent();
    }

    public function test_the_rate_is_cached(): void
    {
        Http::fake([self::URL => Http::response($this->success())]);

        $this->rates()->perUsd('MXN');
        $this->rates()->perUsd('MXN');

        Http::assertSentCount(1);
    }

    public function test_only_the_configured_currencies_are_kept(): void
    {
        Http::fake([self::URL => Http::response($this->success())]);

        $this->rates()->perUsd('MXN');

        $this->assertSame(['MXN' => 18.5], Cache::get('exchange-rate.usd'));
    }

    public function test_a_failed_request_reuses_the_last_good_rate(): void
    {
        Http::fakeSequence(self::URL)
            ->push($this->success(18.5))
            ->pushStatus(500);

        $this->assertSame(18.5, $this->rates()->perUsd('MXN'));

        // The cached copy expires and the API is now failing.
        Cache::forget('exchange-rate.usd');

        $this->assertSame(18.5, $this->rates()->perUsd('MXN'));
    }

    public function test_with_nothing_stored_the_configured_fallback_is_used(): void
    {
        config(['services.exchange_rate.fallback' => ['MXN' => 19.0]]);
        Http::fake([self::URL => Http::response('', 500)]);

        $this->assertSame(19.0, $this->rates()->perUsd('MXN'));
    }

    public function test_a_failure_is_not_retried_straight_away(): void
    {
        Http::fake([self::URL => Http::response('', 500)]);

        $this->rates()->perUsd('MXN');
        $this->rates()->perUsd('MXN');

        // The failure is remembered for the retry window, so one request only.
        Http::assertSentCount(1);
    }

    public function test_a_failure_is_tried_again_after_the_retry_window(): void
    {
        config(['services.exchange_rate.retry_ttl' => 0]);
        Http::fakeSequence(self::URL)->pushStatus(500)->push($this->success(17.9));

        $this->assertSame(18.3, $this->rates()->perUsd('MXN'));

        Cache::forget('exchange-rate.usd');

        $this->assertSame(17.9, $this->rates()->perUsd('MXN'));
    }

    public function test_an_error_answer_is_not_used(): void
    {
        Http::fake([self::URL => Http::response(['result' => 'error', 'error-type' => 'unsupported-code'])]);

        $this->assertSame(18.3, $this->rates()->perUsd('MXN'));
    }

    public function test_an_answer_without_the_currency_is_not_used(): void
    {
        Http::fake([self::URL => Http::response(['result' => 'success', 'rates' => ['EUR' => 0.9]])]);

        $this->assertSame(18.3, $this->rates()->perUsd('MXN'));
    }

    public function test_a_zero_rate_is_not_used(): void
    {
        Http::fake([self::URL => Http::response($this->success(0))]);

        $this->assertSame(18.3, $this->rates()->perUsd('MXN'));
    }

    public function test_a_connection_error_falls_back(): void
    {
        Http::fake([self::URL => fn () => throw new ConnectionException('Timed out.')]);

        $this->assertSame(18.3, $this->rates()->perUsd('MXN'));
    }

    public function test_a_currency_with_no_rate_and_no_fallback_is_an_error(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->rates()->perUsd('JPY');
    }
}
