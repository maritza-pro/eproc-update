<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity,
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
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function procurementItems(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }
}
