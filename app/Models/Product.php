<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    const array TYPES = [
        self::TYPE_GOODS,
        self::TYPE_SERVICES,
    ];

    const string TYPE_GOODS = 'goods';

    const string TYPE_SERVICES = 'services';

    protected $fillable = [
        'name',
        'type',
        'unit',
        'description',
        'self_estimated_price',
    ];

    public function bidItems(): HasMany
    {
        return $this->hasMany(BidItem::class, 'procurement_item_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function procurementItems(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }
}
