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
        Schema::create('survey_question_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('survey_question_id')->constrained('survey_questions')->comment('The survey question this option belongs to');
            $table->string('option')->comment('The option text');
            $table->integer('points')->nullable()->default(0)->comment('The points associated with the option');
            $table->boolean('is_correct')->default(false)->comment('Indicates if the option is correct');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_question_options');
    }
};
