<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Procurement extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    const array METHODS = [
        self::METHOD_TENDER,
        self::METHOD_DIRECT,
    ];

    const string METHOD_DIRECT = 'direct';

    const string METHOD_TENDER = 'tender';

    protected $fillable = [
        'title',
        'description',
        'method',
        'start_date',
        'end_date',
    ];

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }
}
