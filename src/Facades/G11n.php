<?php

declare(strict_types=1);

namespace Victormgomes\G11n\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void setContext(array $preferences)
 * @method static string getTimezone()
 * @method static string getLocale()
 * @method static string getCurrency()
 * @method static string getDateFormat()
 * @method static string getTimeFormat()
 * @method static string getDateTimeFormat()
 * @method static \Illuminate\Support\Carbon localize(\Illuminate\Support\Carbon $date)
 * @method static string format(\Illuminate\Support\Carbon $date)
 * @method static array getSettings()
 * @method static string formatMoney(int|float $amount, ?string $currency = null, bool $includeSymbol = true)
 * @method static string formatNumber(int|float $number, int $precision = 2)
 * @method static string getDirection()
 * @method static string getMeasurementSystem()
 * @method static int getWeekStart()
 * @method static void bootstrap($entity, string $countryCode)
 *
 * @see \Victormgomes\G11n\G11n
 */
class G11n extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Victormgomes\G11n\G11n::class;
    }
}
