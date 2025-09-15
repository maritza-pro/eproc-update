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
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procurement_schedule_id')->constrained('procurement_schedules')->cascadeOnDelete()->comment('The procurement schedule this requirement belongs to');
            $table->string('title')->comment('The title of the requirement');
            $table->string('type')->comment('The type of the requirement');
            $table->boolean('is_required')->default(false)->comment('Indicates if the requirement is required');
            $table->text('description')->nullable()->comment('The description of the requirement');
            $table->integer('points')->nullable()->default(0)->comment('The points associated with the requirement');

            $table->timestampSoftDelete();
        });

        Schema::create('requirement_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('requirement_id')->constrained('requirements')->cascadeOnDelete()->comment('The requirement this option belongs to');
            $table->string('label')->comment('The option text');
            $table->integer('points')->nullable()->default(0)->comment('The points associated with the option');
            $table->boolean('is_correct')->default(false)->comment('Indicates if the option is correct');

            $table->timestampSoftDelete();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete()->comment('The vendor this submission belongs to');
            $table->foreignId('procurement_schedule_id')->constrained('procurement_schedules')->cascadeOnDelete()->comment('The procurement schedule this submission belongs to');
            $table->boolean('is_reviewed')->default(false)->comment('Indicates if the submission is reviewed');
            $table->integer('total_points')->nullable()->default(0)->comment('The total points associated with the submission');
            $table->integer('max_points')->nullable()->default(0)->comment('The maximum points associated with the submission');
            $table->text('notes')->nullable()->comment('The notes associated with the submission');

            $table->timestampSoftDelete();
        });

        Schema::create('submission_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete()->comment('The submission this answer belongs to');
            $table->foreignId('requirement_id')->constrained('requirements')->cascadeOnDelete()->comment('The requirement this answer belongs to');
            $table->text('answer')->nullable()->comment('The answer text');
            $table->integer('points')->nullable()->default(0)->comment('The points associated with the answer');

            $table->timestampSoftDelete();
        });

        Schema::create('submission_answer_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('submission_answer_id')->constrained('submission_answers')->cascadeOnDelete()->comment('The submission answer this detail belongs to');
            $table->foreignId('requirement_option_id')->constrained('requirement_options')->cascadeOnDelete()->comment('The requirement this detail belongs to');

            $table->timestampSoftDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_answer_options');

        Schema::dropIfExists('submission_answers');

        Schema::dropIfExists('submissions');

        Schema::dropIfExists('requirement_options');

        Schema::dropIfExists('requirements');
    }
};
