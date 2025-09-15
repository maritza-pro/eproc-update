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
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('survey_id')->constrained('surveys')->comment('The survey this question belongs to');
            $table->string('type')->comment('The type of the question');
            $table->string('question')->comment('The question text');
            $table->text('description')->nullable()->comment('The description of the question');
            $table->boolean('required')->nullable()->default(false)->comment('Indicates if the question is required');
            $table->integer('points')->nullable()->default(0)->comment('The points associated with the question');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
