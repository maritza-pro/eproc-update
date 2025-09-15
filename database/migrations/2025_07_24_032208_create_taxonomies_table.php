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
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();

            $table->string('model')->comment('The model associated with the taxonomy');
            $table->string('name')->comment('The name of the taxonomy');
            $table->string('slug')->unique()->comment('The slug of the taxonomy');
            $table->string('type')->comment('The type of the taxonomy');
            $table->string('code')->nullable()->comment('The code of the taxonomy');
            $table->string('description')->nullable()->comment('The description of the taxonomy');
            $table->string('parent_id')->nullable()->comment('The parent ID of the taxonomy');
            $table->string('text_color')->nullable()->comment('The text color of the taxonomy');
            $table->string('background_color')->nullable()->comment('The background color of the taxonomy');
            $table->boolean('is_active')->default(true)->comment('Indicates if the taxonomy is active');
            $table->boolean('is_system')->default(false)->comment('Indicates if the taxonomy is system-generated');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxonomies');
    }
};
