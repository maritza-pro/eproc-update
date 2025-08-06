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
        // TODO : jangan pakai singkatan untuk nama table, contoh : vendor_contacts
        Schema::create('vendor_contacts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained()->onDelete('cascade')->comment('The ID of the vendor associated with the profile');
            $table->string('name')->nullable()->comment('Full name of the PIC');
            $table->string('position')->nullable()->comment('Job title or position of the PIC');
            $table->string('phone_number')->nullable()->comment('Phone number of the PIC');
            $table->string('email')->nullable()->comment('Email address of the PIC');
            // TODO : jangan pakai singkatan untuk nama table, contoh : identity_number dari pada ktp_number
            $table->string('identity_number')->nullable()->comment('National ID (KTP) number of the PIC');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_contacts');
    }
};
