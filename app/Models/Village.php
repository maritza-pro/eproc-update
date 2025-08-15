<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Village extends Model
{
    //
    use LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'name',
        'district_id',
        'latitude',
        'longitude',
    ];

    /**
     * Get the district associated with the village.
     * Defines a belongs-to relationship with the District model.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    /**
     * Get the options for logging activity.
     *
     * Configures the activity log options for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
