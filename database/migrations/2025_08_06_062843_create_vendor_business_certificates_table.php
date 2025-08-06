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
        Schema::create('vendor_business_certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained()->onDelete('cascade')->comment('The ID of the vendor associated with the business certificate');
            $table->string('certificate_number')->nullable()->comment('The certificate number of the business certificate');
            $table->date('issued_at')->nullable()->comment('The date the business certificate was issued');
            $table->string('issued_by')->nullable()->comment('The entity that issued the business certificate');
            $table->date('expires_at')->nullable()->comment('The expiration date of the business certificate');
            $table->string('classification')->nullable()->comment('The classification of the business certificate');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_business_certificates');
    }
};
