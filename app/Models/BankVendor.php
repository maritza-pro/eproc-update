<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankVendor extends Model
{
    //
    use HasFactory,
		LogsActivity,
        Cachable,
        SoftDeletes;

    protected $fillable = [
        'bank_id',
        'account_name',
        'account_number',
        'branch_name',
        'is_active',
    ];

	public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
