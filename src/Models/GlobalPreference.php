<?php

declare(strict_types=1);

namespace Victormgomes\G11n\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GlobalPreference extends Model
{
    protected $fillable = [
        'preferable_id',
        'preferable_type',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function preferable(): MorphTo
    {
        return $this->morphTo();
    }
}
