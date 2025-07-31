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
		Schema::table('vendors', function (Blueprint $table) {
			if (Schema::hasColumn('vendors', 'bank_name')) {
				$table->dropColumn(['bank_name', 'bank_account_number', 'bank_account_name', 'bank_branch']);
			}

			if (!Schema::hasColumn('vendors', 'bank_id')) {
				$table->foreignId('bank_id')->nullable()->constrained('banks')->after('license_number');
			}
		});
	}

    /**
     * Reverse the migrations.
     */
    public function down(): void
	{
		Schema::table('vendors', function (Blueprint $table) {
			$table->dropForeign(['bank_id']);
			$table->dropColumn('bank_id');
			$table->string('bank_name')->nullable();
			$table->string('bank_account_number')->nullable();
			$table->string('bank_account_name')->nullable();
			$table->string('bank_branch')->nullable();
		});
	}
};
