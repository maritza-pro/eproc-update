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
        Schema::create('vendor_deeds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade')->comment('Reference to the vendor');
            $table->string('deed_number')->nullable()->comment('Company Deed Number');
            $table->date('deed_date')->nullable()->comment('Company Deed Date');
            $table->string('deed_notary_name')->nullable()->comment('Notary Name for Company Deed');
            $table->string('approval_number')->nullable()->comment('Approval Number from Ministry of Law and Human Rights');
            $table->string('latest_amendment_number')->nullable()->comment('Latest Amendment Deed Number');
            $table->date('latest_amendment_date')->nullable()->comment('Latest Amendment Deed Date');
            $table->string('latest_amendment_notary')->nullable()->comment('Notary Name for Latest Amendment');
            $table->string('latest_approval_number')->nullable()->comment('Approval Number for Latest Amendment');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_deeds');
    }
};
