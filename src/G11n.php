<?php

declare(strict_types=1);

namespace Victormgomes\G11n;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use Illuminate\Support\Carbon;
use NumberFormatter;
use Victormgomes\G11n\Services\GlobalizationService;

class G11n
{
    protected ?string $timezone = null;

    protected ?string $locale = null;

    protected ?string $currency = null;

    protected ?string $dateFormat = null;

    protected ?string $timeFormat = null;

    public function setContext(array $preferences): void
    {
        $this->timezone = $preferences['timezone'] ?? null;
        $this->locale = $preferences['locale'] ?? null;
        $this->currency = $preferences['currency'] ?? null;
        $this->dateFormat = $preferences['date_format'] ?? null;
        $this->timeFormat = $preferences['time_format'] ?? null;
    }

    public function getTimezone(): string
    {
        return $this->timezone ?? config('app.timezone', 'UTC');
    }

    public function getLocale(): string
    {
        return $this->locale ?? config('app.locale', 'en');
    }

    public function getCurrency(): string
    {
        return $this->currency ?? config('app.currency', 'USD');
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat ?? 'd/m/Y';
    }

    public function getTimeFormat(): string
    {
        return $this->timeFormat ?? 'H:i:s';
    }

    public function getDateTimeFormat(): string
    {
        return $this->getDateFormat().' '.$this->getTimeFormat();
    }

    public function localize(Carbon $date): Carbon
    {
        return $date->copy()->timezone($this->getTimezone());
    }

    public function format(Carbon $date): string
    {
        return $this->localize($date)->format($this->getDateTimeFormat());
    }

    /**
     * Get all active globalization settings for the current context.
     */
    public function getSettings(): array
    {
        return [
            'timezone' => $this->getTimezone(),
            'locale' => $this->getLocale(),
            'currency' => $this->getCurrency(),
            'date_format' => $this->getDateFormat(),
            'time_format' => $this->getTimeFormat(),
            'direction' => $this->getDirection(),
            'measurement_system' => $this->getMeasurementSystem(),
            'week_start' => $this->getWeekStart(),
        ];
    }

    /**
     * Format money based on active currency and locale.
     */
    public function formatMoney(int|float $amount, ?string $currency = null, bool $includeSymbol = true): string
    {
        $currency = $currency ?? $this->getCurrency();
        $money = new Money($amount, new Currency($currency));

        if (! $includeSymbol) {
            return (string) $money->getAmount();
        }

        return $money->format();
    }

    /**
     * Format a number based on the active locale.
     */
    public function formatNumber(int|float $number, int $precision = 2): string
    {
        $formatter = new NumberFormatter($this->getLocale(), NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $precision);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $precision);

        return $formatter->format($number) ?: (string) $number;
    }

    /**
     * Get the text direction for the current locale.
     */
    public function getDirection(): string
    {
        $rtlLocales = ['ar', 'he', 'fa', 'ur', 'dv'];
        $baseLocale = explode('_', $this->getLocale())[0];

        return in_array($baseLocale, $rtlLocales) ? 'rtl' : 'ltr';
    }

    /**
     * Get the measurement system for the current locale/country.
     */
    public function getMeasurementSystem(): string
    {
        $imperialCountries = ['US', 'LR', 'MM'];
        $countryCode = $this->getCountryFromLocale();

        return in_array($countryCode, $imperialCountries) ? 'imperial' : 'metric';
    }

    /**
     * Get the first day of the week (0 = Sunday, 1 = Monday).
     */
    public function getWeekStart(): int
    {
        $sundayStartCountries = ['US', 'CA', 'MX', 'BR', 'IL', 'JP', 'PH', 'KR'];
        $countryCode = $this->getCountryFromLocale();

        return in_array($countryCode, $sundayStartCountries) ? 0 : 1;
    }

    protected function getCountryFromLocale(): string
    {
        $parts = explode('_', $this->getLocale());

        return isset($parts[1]) ? strtoupper($parts[1]) : 'US';
    }

    public function bootstrap($entity, string $countryCode): void
    {
        $service = app(GlobalizationService::class);
        $defaults = $service->getDefaultsForCountry($countryCode);

        if (empty($defaults)) {
            return;
        }

        if (method_exists($entity, 'setPreferences')) {
            $entity->setPreferences([
                'timezone' => $defaults['timezone'],
                'currency' => $defaults['currency'],
                'locale' => $defaults['locale'],
            ]);
        }
    }
}
