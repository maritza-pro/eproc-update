<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;



class BusinessField extends Model
{
    use LogsActivity;
    use SoftDeletes;
	use HasFactory;

   protected $fillable = [
        'code',
        'name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

	public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }
}
