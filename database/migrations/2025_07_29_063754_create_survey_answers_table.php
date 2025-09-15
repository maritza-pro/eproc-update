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
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();

            $table->morphs('answerable');
            $table->foreignId('survey_id')->constrained('surveys')->comment('The survey this answer belongs to');
            $table->integer('points')->nullable()->comment('The points associated with the answer');
            $table->integer('max_points')->nullable()->comment('The maximum points associated with the answer');
            $table->integer('answered')->nullable()->comment('The number of questions answered');
            $table->string('comment')->nullable()->comment('The comment associated with the answer');
            $table->boolean('is_reviewed')->default(false)->comment('Indicates if the answer is reviewed');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
    }
};
