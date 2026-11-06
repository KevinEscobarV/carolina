<?php

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
        Schema::table('promises', function (Blueprint $table) {
            $table->string('interest_rate_type')->default('monthly')->after('interest_rate');
            $table->json('extraordinary_months')->nullable()->after('number_of_fees');
            $table->decimal('extraordinary_amount', 15, 2)->nullable()->after('extraordinary_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promises', function (Blueprint $table) {
            $table->dropColumn(['interest_rate_type', 'extraordinary_months', 'extraordinary_amount']);
        });
    }
};
