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
        Schema::create('agenda_procurements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agenda_id')->constrained('taxonomies')->cascadeOnDelete()->comment('The agenda this procurement belongs to');
            $table->foreignId('procurement_id')->constrained()->cascadeOnDelete()->comment('The procurement this agenda belongs to');
            $table->boolean('is_submission_needed')->default(false)->comment('Indicates if submission is needed for this procurement');
            $table->date('start_date')->nullable()->comment('The start date of the procurement Agenda');
            $table->date('end_date')->nullable()->comment('The end date of the procurement Agenda');
            $table->text('description')->nullable()->comment('The description of the procurement Agenda');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_procurements');
    }
};
