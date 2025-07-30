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
        Schema::create('survey_answer_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('answer_id')->constrained('survey_answers')->comment('The survey answer this detail belongs to');
            $table->foreignId('question_id')->constrained('survey_questions')->comment('The survey question this detail belongs to');
            $table->foreignId('option_id')->nullable()->constrained('survey_question_options')->comment('The survey option this detail belongs to');
            $table->json('answers')->nullable()->comment('The answers for the question');
            $table->integer('points')->nullable()->comment('The points associated with the answer');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answer_details');
    }
};
