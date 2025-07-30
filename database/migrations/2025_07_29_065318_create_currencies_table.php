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
    Schema::create('currencies', function (Blueprint $table) {
        $table->id();
        $table->string('name')
			->comment('The name of the currency, e.g., Indonesian Rupiah');
        $table->string('code', 3)->unique()
			->comment('The code of the currency, e.g., IDR');
        $table->string('symbol', 5)->unique()
			->comment('The symbol of the currency, e.g., Rp');
        $table->unsignedTinyInteger('decimals')->default(2)->comment('Number of decimal places');
        $table->string('symbol_position', 10)->default('left')->comment('Posisition symbol: left or right');
        $table->boolean('is_default')->default(false)
			->comment('Whether the currency is the default currency or not');
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
