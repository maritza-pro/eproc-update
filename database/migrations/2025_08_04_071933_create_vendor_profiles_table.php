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
        Schema::create('vendor_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained()->onDelete('cascade')->comment('The ID of the vendor associated with the profile');
            $table->string('business_entity_type')->nullable()->comment('The type of business entity the company is');
            $table->string('npwp')->nullable()->comment('The NPWP of the company');
            $table->string('nib')->nullable()->comment('The NIB of the company');
            $table->text('head_office_address')->nullable()->comment('The head office address of the company');
            $table->string('website')->nullable()->comment('The website of the company');
            $table->year('established_year')->nullable()->comment('The year the company was established');
            $table->integer('employee_count')->nullable()->comment('The number of employees in the company');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_profiles');
    }
};
