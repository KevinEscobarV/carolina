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
        Schema::create('deeds', function (Blueprint $table) {
            $table->id();

            $table->string('number')->nullable()->unique()->index();
            $table->decimal('value', 15, 2)->default(0);
            $table->date('signature_date')->nullable();
            $table->string('book')->nullable();

            $table->string('status');
            $table->mediumText('observations')->nullable();

            $table->foreignId('parcel_id')
                ->nullable()
                ->unique()
                ->constrained('parcels')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deeds');
    }
};
