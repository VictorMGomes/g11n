<?php

declare(strict_types=1);

namespace Victormgomes\G11n\Services;

use PragmaRX\Countries\Package\Countries;

class GlobalizationService
{
    protected Countries $countries;

    public function __construct()
    {
        $this->countries = new Countries;
    }

    public function getDefaultsForCountry(string $countryCode): array
    {
        $country = $this->countries->where('cca2', strtoupper($countryCode))->first();

        if (! $country) {
            return [];
        }

        $currency = isset($country->currencies) ? $country->currencies->first() : null;
        $timezone = isset($country->timezones) ? $country->timezones->first() : null;

        return [
            'country_name' => $country->name->common ?? $countryCode,
            'currency' => $currency ? ($currency->iso_code ?? $currency) : 'USD',
            'timezone' => $timezone ? ($timezone->zone_name ?? $timezone) : 'UTC',
            'locale' => $this->inferLocale($countryCode),
        ];
    }

    protected function inferLocale(string $countryCode): string
    {
        // Simple mapping for now, can be improved
        $mapping = [
            'BR' => 'pt_BR',
            'US' => 'en_US',
            'GB' => 'en_GB',
            'ES' => 'es_ES',
            'PT' => 'pt_PT',
        ];

        return $mapping[strtoupper($countryCode)] ?? 'en';
    }
}
