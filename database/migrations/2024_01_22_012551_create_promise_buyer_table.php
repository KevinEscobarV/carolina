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
        Schema::create('promise_buyer', function (Blueprint $table) {
            $table->unsignedBigInteger('promise_id');
            $table->unsignedBigInteger('buyer_id');

            $table->foreign('promise_id')
                ->references('id')
                ->on('promises')
                ->onDelete('cascade');

            $table->foreign('buyer_id')
                ->references('id')
                ->on('buyers')
                ->onDelete('cascade');

            $table->primary(['promise_id', 'buyer_id'], 'promise_buyer_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promise_buyer');
    }
};
