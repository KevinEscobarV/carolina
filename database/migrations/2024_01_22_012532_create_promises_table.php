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
        Schema::create('promises', function (Blueprint $table) {
            $table->id();

            $table->date('signature_date');

            $table->decimal('value', 15, 2)->default(0);
            $table->decimal('initial_fee', 15, 2)->default(0);
            $table->integer('number_of_fees')->default(0);
            $table->float('interest_rate')->default(0);

            $table->date('cut_off_date')->nullable();
            $table->string('payment_frequency')->nullable();

            $table->decimal('deed_value', 15, 2)->default(0);
            $table->string('deed_number')->nullable();
            $table->date('deed_date')->nullable();

            $table->string('payment_method')->nullable();

            $table->string('observations')->nullable();

            $table->string('status')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_parcel');
    }
};
