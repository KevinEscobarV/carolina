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
        Schema::create('quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promise_id')->constrained('promises')->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedSmallInteger('number');
            $table->date('due_date');

            $table->decimal('amount', 15, 2);
            $table->decimal('interest', 15, 2)->default(0);
            $table->decimal('principal', 15, 2);
            $table->decimal('balance', 15, 2);
            $table->boolean('is_extraordinary')->default(false);
            // An extra payment applied to the balance ("Reproyectar saldo"); it charges no interest
            $table->boolean('is_prepayment')->default(false);

            $table->timestamps();

            $table->unique(['promise_id', 'number']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotas');
    }
};
