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
            $table->foreignId('category_id')
            ->default(1)
            ->constrained('categories')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });

        Schema::table('buyers', function (Blueprint $table) {
            $table->foreignId('category_id')
            ->default(1)
            ->constrained('categories')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });

        Schema::table('parcels', function (Blueprint $table) {
            $table->foreignId('category_id')
            ->default(1)
            ->constrained('categories')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });

        Schema::table('deeds', function (Blueprint $table) {
            $table->foreignId('category_id')
            ->default(1)
            ->constrained('categories')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('category_id')
            ->default(1)
            ->constrained('categories')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promises', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::table('buyers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::table('parcels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::table('deeds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
