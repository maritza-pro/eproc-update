<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Evaluation extends Model
{
    //
    use LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'bid_id',
        'technical_score',
        'price_score',
        'notes',
    ];

    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
