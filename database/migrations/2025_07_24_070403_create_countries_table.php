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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('The name of the country');
            $table->string('code', 2)->nullable()->comment('The 2-letter country code (e.g., ID, US)');
            $table->string('iso', 3)->nullable()->comment('The 3-letter ISO code of the country');
            $table->string('num_code', 3)->nullable()->comment('The numeric ISO code of the country');
            $table->string('currency')->nullable()->comment('The currency used in the country');
            $table->string('msisdn_code')->nullable()->comment('The MSISDN code used for mobile numbering');
            $table->decimal('latitude', 10, 6)->nullable()->comment('The latitude of the country');
            $table->decimal('longitude', 10, 6)->nullable()->comment('The longitude of the country');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
