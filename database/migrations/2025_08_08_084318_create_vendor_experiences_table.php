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
        Schema::create('vendor_experiences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete()->comment('The vendor this experience belongs to');
            $table->foreignId('business_field_id')->nullable()->constrained('taxonomies')->comment('The ID of the business field associated with the experience');
            $table->string('project_name')->nullable()->comment('The name of the project');
            $table->text('description')->nullable()->comment('The description of the project');
            $table->string('location')->nullable()->comment('The location of the project');
            $table->string('stakeholder')->nullable()->comment('The stakeholder of the project');
            $table->string('contract_number')->nullable()->comment('The contract number of the project');
            $table->date('start_date')->nullable()->comment('The start date of the project');
            $table->date('end_date')->nullable()->comment('The end date of the project');
            $table->string('project_value')->nullable()->comment('The value of the project');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_experiences');
    }
};
