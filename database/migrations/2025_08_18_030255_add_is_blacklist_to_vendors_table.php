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
        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean('is_blacklisted')->default(false)->after('business_field_id')->comment('Indicates if the vendor is blacklisted');
            $table->Text('blacklist_reason')->nullable()->after('rejection_reason')->comment('The reason for blacklisting the vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'is_blacklisted',
                'blacklist_reason',
            ]);
        });
    }
};
