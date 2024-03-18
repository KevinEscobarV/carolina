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
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();

            $table->integer('number');

            $table->string('position')->nullable();
            $table->geography('location', subtype: 'point', srid: 4326)->nullable();
            $table->geometry('area', subtype: 'polygon', srid: 0)->nullable();
            $table->decimal('area_m2', 15, 2)->nullable();
            $table->decimal('value', 15, 2)->default(0);

            $table->foreignId('block_id')->constrained('blocks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('promise_id')->nullable()->constrained('promises')->nullOnDelete()->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
