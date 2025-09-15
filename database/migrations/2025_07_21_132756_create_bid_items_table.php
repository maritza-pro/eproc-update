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
        Schema::create('bid_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bid_id')->constrained()->cascadeOnDelete()->comment('The bid this item belongs to');
            $table->foreignId('procurement_item_id')->constrained()->cascadeOnDelete()->comment('The procurement item this bid item is for');
            $table->decimal('unit_price', 15, 2)->comment('The unit price offered for this item');
            $table->integer('offered_quantity')->nullable()->comment('The quantity offered for this item');
            $table->text('notes')->nullable()->comment('Additional notes or comments for this bid item');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bid_items');
    }
};
