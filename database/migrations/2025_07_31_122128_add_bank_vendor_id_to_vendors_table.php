<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreignId('bank_vendor_id')
                ->nullable()
                ->after('license_number')
                ->constrained('bank_vendors')
                ->nullOnDelete()
                ->comment('Reference to the bank vendor associated with the vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['bank_vendor_id']);
            $table->dropColumn('bank_vendor_id');
        });
    }
};
