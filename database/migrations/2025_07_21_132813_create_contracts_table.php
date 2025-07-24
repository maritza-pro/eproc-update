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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procurement_id')->comment('The procurement this contract is associated with')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->comment('The vendor this contract is with')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->comment('Unique identifier for the contract');
            $table->date('signed_date')->comment('The date the contract was signed');
            $table->decimal('value', 15, 2)->comment('The total value of the contract');
            $table->string('status')->comment('The current status of the contract')->default('draft');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
