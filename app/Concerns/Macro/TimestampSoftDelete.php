<?php

declare(strict_types = 1);

namespace App\Concerns\Macro;

use Illuminate\Database\Schema\Blueprint;

trait TimestampSoftDelete
{
    /**
     * Register Log Time Macro.
     *
     * Registers a macro to add timestamp columns to a database table.
     */
    public function registerTimestampSoftDelete(): void
    {
        Blueprint::macro('timestampSoftDelete', function ($index = true) {
            $this->timestamp('created_at');
            $this->timestamp('updated_at');
            $this->timestamp('deleted_at')->nullable();

            if ($index) {
                if (config('database.default') == 'mysql') {
                    $this->index('created_at', 'created_at');
                    $this->index('updated_at', 'updated_at');
                    $this->index('deleted_at', 'deleted_at');
                } else {
                    $this->index('created_at');
                    $this->index('updated_at');
                    $this->index('deleted_at');
                }
            }

        });
    }
}
