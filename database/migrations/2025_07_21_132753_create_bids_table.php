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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete()->comment('The vendor who submitted the bid');
            $table->foreignId('procurement_id')->constrained()->cascadeOnDelete()->comment('The procurement this bid is for');
            $table->text('notes')->nullable()->comment('Additional notes or comments for the bid');
            $table->string('status')->default('submitted')->comment('The current status of the bid');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
