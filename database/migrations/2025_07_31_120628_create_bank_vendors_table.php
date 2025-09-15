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
        Schema::create('bank_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete()->comment('The bank this account is for');
            $table->string('account_name')->comment('The name of the account holder');
            $table->string('account_number')->comment('The account number');
            $table->string('branch_name')->nullable()->comment('The branch name of the bank');
            $table->boolean('is_active')->default(true)->comment('Indicates if the account is active');
            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_vendors');
    }
};
