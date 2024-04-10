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
            $table->integer('number_of_fees')->default(1);
            $table->json('projection')->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('is_initial_fee')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promises', function (Blueprint $table) {
            $table->dropColumn('number_of_fees');
            $table->dropColumn('projection');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('is_initial_fee');
        });
    }
};
