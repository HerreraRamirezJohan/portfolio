<?php

namespace App\Support;

/**
 * An hourly rate stored in US dollars, as shown in a locale's currency.
 *
 * The locale -> currency map is `portfolio.currencies`. Dollars pass through
 * untouched (and never call the exchange-rate service); any other currency
 * is converted at the current rate and rounded to `portfolio.rate_rounding`
 * units, so a page shows $275 rather than $274.52.
 */
final class HourlyRates
{
    public function __construct(private readonly ExchangeRate $exchangeRate) {}

    /**
     * @return array{amount: int, currency: string, converted: bool}
     */
    public function forLocale(int $usd, string $locale): array
    {
        $currency = strtoupper((string) config("portfolio.currencies.{$locale}", 'USD'));

        if ($currency === 'USD') {
            return ['amount' => $usd, 'currency' => 'USD', 'converted' => false];
        }

        $step = max(1, (int) config('portfolio.rate_rounding', 5));

        return [
            'amount' => (int) (round($usd * $this->exchangeRate->perUsd($currency) / $step) * $step),
            'currency' => $currency,
            'converted' => true,
        ];
    }
}
