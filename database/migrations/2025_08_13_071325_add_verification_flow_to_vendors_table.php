<?php

declare(strict_types = 1);

use App\Enums\VendorStatus;
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
            $table->dropColumn('is_verified');

            $table->enum('verification_status', array_column(VendorStatus::cases(), 'value'))->default(VendorStatus::Pending->value)->after('license_number')->comment('The verification status of the vendor');
            $table->text('rejection_reason')->nullable()->after('verification_status')->comment('The rejection reason for the vendor');
            $table->foreignId('verified_by')->nullable()->after('rejection_reason')->constrained('users')->comment('The user who verified the vendor');
            $table->timestamp('verified_at')->nullable()->after('verified_by')->comment('The date and time the vendor was verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);

            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn([
                'verification_status',
                'rejection_reason',
                'verified_at',
            ]);
        });
    }
};
