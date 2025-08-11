<?php

declare(strict_types = 1);

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
            if (Schema::hasColumn('vendors', 'bank_vendor_id')) {
                $table->dropForeign(['bank_vendor_id']);
                $table->dropColumn('bank_vendor_id');
            }
        });

        Schema::table('bank_vendors', function (Blueprint $table) {
            $table->foreignId('vendor_id')
                ->constrained()
                ->nullable()
                ->onDelete('cascade')
                ->comment('The ID of the vendor associated with the bank vendor');
            $table->unique(['vendor_id', 'bank_id'])->comment('Unique constraint for vendor_id and bank_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_vendors', function (Blueprint $table) {
            $table->dropUnique(['vendor_id', 'bank_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->foreignId('bank_vendor_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null')
                ->comment('Reference to the bank vendor associated with the vendor');
        });
    }
};
