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
        Schema::table('vendors', function (Blueprint $table): void {
            $table->dropForeign(['business_field_id']);
            $table->dropColumn('business_field_id');
        });
        Schema::dropIfExists('business_fields');

        Schema::table('vendors', function (Blueprint $table): void {
            $table->foreignId('business_field_id')
                ->nullable()
                ->after('company_name')
                ->constrained('taxonomies')
                ->comment('The ID of the business field associated with the vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table): void {
            $table->dropForeign(['business_field_id']);
            $table->dropColumn('business_field_id');
        });

        Schema::create('business_fields', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('vendors', function (Blueprint $table): void {
            $table->foreignId('business_field_id')
                ->nullable()
                ->after('company_name')
                ->constrained('business_fields')
                ->onDelete('set null')
                ->comment('The ID of the business field associated with the vendor');
        });
    }
};
