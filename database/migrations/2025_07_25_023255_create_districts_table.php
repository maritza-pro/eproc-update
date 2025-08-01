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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('city_id')->constrained()->cascadeOnDelete()->comment('The city this district belongs to');
            $table->string('name')->comment('The name of the district');
            $table->decimal('latitude', 10, 6)->nullable()->comment('The latitude of the district');
            $table->decimal('longitude', 10, 6)->nullable()->comment('The longitude of the district');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
