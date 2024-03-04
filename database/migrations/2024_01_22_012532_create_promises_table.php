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

            $table->string('number')->unique()->index();
            
            $table->date('signature_date');

            $table->decimal('value', 15, 2)->default(0);
            $table->decimal('initial_fee', 15, 2)->default(0);
            $table->decimal('quota_amount', 15, 2)->default(0);
            $table->float('interest_rate')->default(0);

            $table->date('cut_off_date')->nullable();
            $table->string('payment_frequency')->nullable();

            $table->string('payment_method')->nullable();

            $table->string('status')->nullable();
            $table->mediumText('observations')->nullable();

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
