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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();

            $table->string('company_name')->comment('The name of the vendor company');
            $table->string('email')->unique()->comment('The email address of the vendor');
            $table->string('phone')->nullable()->comment('The phone number of the vendor');
            $table->string('tax_number')->nullable()->comment('The tax number of the vendor');
            $table->string('business_number')->nullable()->comment('The business registration number of the vendor');
            $table->string('license_number')->nullable()->comment('The license number of the vendor');
            $table->boolean('is_verified')->default(false)->comment('Indicates if the vendor is verified');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('The ID of the user associated with the vendor');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
