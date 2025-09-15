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
        Schema::create('taxonomy_relations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('taxonomy_id')->constrained()->cascadeOnDelete()->comment('The taxonomy this relation belongs to');
            $table->morphs('relationable');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxonomy_relations');
    }
};
