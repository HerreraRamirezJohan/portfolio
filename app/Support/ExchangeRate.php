<?php

namespace App\Support;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Client\Factory as Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

/**
 * How many units of a currency one US dollar buys, from ExchangeRate-API's
 * open-access endpoint (https://www.exchangerate-api.com/docs/free).
 *
 * The page never waits on, or breaks because of, that service:
 *  - a good answer is cached for `services.exchange_rate.ttl` seconds and also
 *    kept for good as the "last good" rate;
 *  - if the call fails (a connection error is retried once; an error answer
 *    is not), the last good rate is used and the call is not repeated for
 *    `retry_ttl` seconds;
 *  - if no call has ever worked, `services.exchange_rate.fallback` is used.
 *
 * Both dependencies are injected by the container, so tests fake the HTTP
 * client and the cache store without touching this class.
 */
final class ExchangeRate
{
    private const FRESH = 'exchange-rate.usd';

    private const LAST_GOOD = 'exchange-rate.usd.last-good';

    public function __construct(
        private readonly Http $http,
        private readonly Cache $cache,
    ) {}

    /**
     * Units of `$currency` per US dollar.
     *
     * @throws InvalidArgumentException when there is neither a rate nor a fallback
     */
    public function perUsd(string $currency): float
    {
        $currency = strtoupper($currency);

        if ($currency === 'USD') {
            return 1.0;
        }

        $rate = $this->rates()[$currency] ?? config("services.exchange_rate.fallback.{$currency}");

        if (! is_numeric($rate) || $rate <= 0) {
            throw new InvalidArgumentException("There is no exchange rate for {$currency}.");
        }

        return (float) $rate;
    }

    /**
     * @return array<string, float>
     */
    private function rates(): array
    {
        $cached = $this->cache->get(self::FRESH);

        if (is_array($cached)) {
            return $cached;
        }

        $fresh = $this->fetch();

        if ($fresh !== null) {
            $this->cache->put(self::FRESH, $fresh, (int) config('services.exchange_rate.ttl'));
            $this->cache->forever(self::LAST_GOOD, $fresh);

            return $fresh;
        }

        $stale = $this->cache->get(self::LAST_GOOD);
        $stale = is_array($stale) ? $stale : [];

        // Remember the failure for a few minutes, so an API that is down is
        // not called again on every page build.
        $this->cache->put(self::FRESH, $stale, (int) config('services.exchange_rate.retry_ttl'));

        return $stale;
    }

    /**
     * One request; null when it fails or answers something unexpected.
     *
     * @return array<string, float>|null
     */
    private function fetch(): ?array
    {
        try {
            $response = $this->http
                ->acceptJson()
                ->timeout(3)
                ->retry(1, 200, throw: false)
                ->get((string) config('services.exchange_rate.url'));
        } catch (Throwable $exception) {
            Log::warning('Exchange rate request failed.', ['error' => $exception->getMessage()]);

            return null;
        }

        $rates = $response->json('rates');

        if ($response->failed() || $response->json('result') !== 'success' || ! is_array($rates)) {
            Log::warning('Exchange rate response was not usable.', [
                'status' => $response->status(),
                'error' => $response->json('error-type'),
            ]);

            return null;
        }

        $kept = [];

        foreach ($this->wantedCurrencies() as $code) {
            $rate = $rates[$code] ?? null;

            if (is_numeric($rate) && $rate > 0) {
                $kept[$code] = (float) $rate;
            }
        }

        return $kept === [] ? null : $kept;
    }

    /**
     * The non-dollar currencies the site shows, from config/portfolio.php.
     *
     * @return array<int, string>
     */
    private function wantedCurrencies(): array
    {
        $codes = array_map('strtoupper', array_values((array) config('portfolio.currencies')));

        return array_values(array_unique(array_diff($codes, ['USD'])));
    }
}
