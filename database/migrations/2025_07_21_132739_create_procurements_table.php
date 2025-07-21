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
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();

            $table->string('title')->comment('The title of the procurement');
            $table->text('description')->nullable()->comment('Description of the procurement');
            $table->string('method')->comment('Method of procurement');
            $table->date('start_date')->comment('Start date of the procurement');
            $table->date('end_date')->nullable()->comment('End date of the procurement');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};
