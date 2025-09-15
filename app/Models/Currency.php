<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Currency extends Model
{
    use Cachable,
        HasFactory,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'decimals',
        'symbol_position',
        'is_default',
    ];

    /**
     * Get activity log options.
     *
     * Defines the configuration for logging activity on this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the products associated with the currency.
     *
     * Defines a has-many relationship with the Product model.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
