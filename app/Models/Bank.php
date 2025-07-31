<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bank extends Model
{
    //
    use LogsActivity,
        Cachable,
        SoftDeletes;

    protected $fillable = [
		'bank_name',
		'bank_account_name',
		'bank_account_number',
		'bank_branch',
	];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
