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
        Schema::table('procurements', function (Blueprint $table) {
            $table->foreignId('business_field_id')
                ->nullable()
                ->constrained('taxonomies')
                ->nullOnDelete()
                ->comment('The ID of the business field associated with the procurement');

            $table->foreignId('method_id')
                ->nullable()
                ->constrained('taxonomies')
                ->restrictOnDelete()
                ->comment('The ID of the procurement type associated with the procurement');

            $table->foreignId('type_id')
                ->nullable()
                ->constrained('taxonomies')
                ->restrictOnDelete()
                ->comment('The ID of the procurement type associated with the procurement');

            $table->decimal('value', 20, 2)
                ->nullable()
                ->comment('The value of the procurement');

            $table->unsignedInteger('quantity')
                ->nullable()
                ->comment('The quantity of the procurement');

            $table->string('number')
                ->nullable()
                ->comment('The number of the procurement');

            $table->dropColumn('method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->string('method')
                ->nullable()
                ->comment('The procurement method');

            $table->dropConstrainedForeignId('business_field_id');
            $table->dropConstrainedForeignId('method_id');
            $table->dropConstrainedForeignId('type_id');

            $table->dropColumn(['value', 'quantity', 'number']);
        });
    }
};
