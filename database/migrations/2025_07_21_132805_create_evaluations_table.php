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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bid_id')->constrained()->cascadeOnDelete()->comment('The bid this evaluation belongs to');
            $table->decimal('technical_score', 5, 2)->nullable()->comment('The technical score assigned to the bid');
            $table->decimal('price_score', 5, 2)->nullable()->comment('The price score assigned to the bid');
            $table->text('notes')->nullable()->comment('Additional notes or comments for the evaluation');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
