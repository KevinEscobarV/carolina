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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->date('agreement_date');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('paid_amount');
            $table->string('payment_method');
            $table->string('observations')->nullable();
            $table->string('bill_path', 2048)->nullable();

            $table->foreignId('promise_id')->constrained('promises')->cascadeOnDelete()->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
