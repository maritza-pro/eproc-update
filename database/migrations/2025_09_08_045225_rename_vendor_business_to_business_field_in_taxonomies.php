<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('taxonomies')
                ->where('model', 'App\Models\VendorBusiness')
                ->where('type', 'vendor-business')
                ->update([
                    'model' => \App\Models\BusinessField::class,
                    'type' => 'business-field',
                    'updated_at' => now(),
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            DB::table('taxonomies')
                ->where('model', \App\Models\BusinessField::class)
                ->where('type', 'business-field')
                ->update([
                    'model' => 'App\Models\VendorBusiness',
                    'type' => 'vendor-business',
                    'updated_at' => now(),
                ]);
        });
    }
};
