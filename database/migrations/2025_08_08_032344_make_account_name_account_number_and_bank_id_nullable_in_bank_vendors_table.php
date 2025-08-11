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
        Schema::table('bank_vendors', function (Blueprint $table) {
            $table->string('account_name')->nullable()->change();
            $table->string('account_number')->nullable()->change();
            $table->foreignId('bank_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_vendors', function (Blueprint $table) {
            $table->string('account_name')->nullable(false)->change();
            $table->string('account_number')->nullable(false)->change();
            $table->foreignId('bank_id')->nullable(false)->change();
        });
    }
};
