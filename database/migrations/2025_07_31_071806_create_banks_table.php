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
		Schema::create('banks', function (Blueprint $table) {
			$table->id();
			$table->string('bank_name')
				->comment('The name of the bank');
			$table->string('bank_account_name')
				->comment('The name of the account holder');
			$table->string('bank_account_number')
				->comment('The bank account number');
			$table->string('bank_branch')
				->nullable()
				->comment('The branch of the bank, if applicable');
			$table->timestampSoftDelete();
		});
	}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
