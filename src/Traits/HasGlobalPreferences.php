<?php

declare(strict_types=1);

namespace Victormgomes\G11n\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Cache;
use Victormgomes\G11n\Models\GlobalPreference;

trait HasGlobalPreferences
{
    public function globalPreference(): MorphOne
    {
        return $this->morphOne(GlobalPreference::class, 'preferable');
    }

    public function getPreference(string $key, mixed $default = null): mixed
    {
        $cacheKey = "g11n_prefs_{$this->getTable()}_{$this->id}";

        $settings = Cache::remember($cacheKey, 3600, function () {
            return $this->globalPreference?->settings ?? [];
        });

        return $settings[$key] ?? $default;
    }

    public function setPreference(string $key, mixed $value): void
    {
        $preference = $this->globalPreference()->firstOrCreate([
            'preferable_id' => $this->id,
            'preferable_type' => $this->getMorphClass(),
        ]);

        $settings = $preference->settings ?? [];
        $settings[$key] = $value;

        $preference->update(['settings' => $settings]);

        Cache::forget("g11n_prefs_{$this->getTable()}_{$this->id}");
    }

    public function setPreferences(array $values): void
    {
        $preference = $this->globalPreference()->firstOrCreate([
            'preferable_id' => $this->id,
            'preferable_type' => $this->getMorphClass(),
        ]);

        $settings = array_merge($preference->settings ?? [], $values);

        $preference->update(['settings' => $settings]);

        Cache::forget("g11n_prefs_{$this->getTable()}_{$this->id}");
    }
}
