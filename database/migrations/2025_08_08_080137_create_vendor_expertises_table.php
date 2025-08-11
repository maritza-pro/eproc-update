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
        Schema::create('vendor_expertises', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete()->comment('The vendor this expertise belongs to');
            $table->string('expertise')->nullable()->comment('The expertise of the vendor');
            $table->string('expertise_level')->nullable()->comment('The level of expertise of the vendor');
            $table->text('description')->nullable()->comment('The description of the expertise');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_expertises');
    }
};
