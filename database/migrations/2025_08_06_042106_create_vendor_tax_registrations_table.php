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
        Schema::create('vendor_tax_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained()->onDelete('cascade')->comment('The ID of the vendor associated with the tax registration');
            $table->string('name')->nullable()->comment('The name of the tax registration');
            $table->text('address')->nullable()->comment('The address attached to the tax registration');
            $table->string('certificate_number')->nullable()->comment('The certificate number of the tax registration');
            $table->string('confirmation_status')->nullable()->comment('The confirmation status of the tax registration');
            $table->string('tax_obligation')->nullable()->comment('The tax obligation of the tax registration');
            $table->string('registered_tax_office')->nullable()->comment('The registered tax office of the tax registration');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_tax_registrations');
    }
};
