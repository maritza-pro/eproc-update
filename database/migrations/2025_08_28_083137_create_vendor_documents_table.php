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
        Schema::create('vendor_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('type')->index()->nullable()->comment('The type of document');
            $table->string('category')->nullable()->comment('The category of the document');
            $table->string('document_number')->nullable()->comment('The document number');
            $table->date('issue_date')->nullable()->comment('The issue date of the document');
            $table->date('expiry_date')->nullable()->comment('The expiry date of the document');
            $table->json('properties')->nullable()->comment('Additional properties of the document');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_documents');
    }
};
