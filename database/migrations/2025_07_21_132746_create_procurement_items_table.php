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
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procurement_id')->constrained()->cascadeOnDelete()->comment('The procurement this item belongs to');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete()->comment('The product associated with this procurement item');
            $table->integer('quantity')->comment('Quantity of the product in this procurement item');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_items');
    }
};
