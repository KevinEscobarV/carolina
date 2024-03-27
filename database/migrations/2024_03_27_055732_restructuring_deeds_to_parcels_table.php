<?php

use App\Models\Scopes\CategoryScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->string('registration_number')->nullable()->unique()->after('area_m2');
        });

        DB::select('UPDATE parcels p
            JOIN deeds d ON p.id = d.parcel_id
            SET p.registration_number = d.number');
        
        DB::select('DELETE FROM deeds');

        Schema::table('deeds', function (Blueprint $table) {
            $table->foreignId('promise_id')
            ->unique()
            ->after('observations')
            ->constrained('promises')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            $table->dropForeign(['parcel_id']);
            $table->dropUnique(['parcel_id']);
            $table->dropColumn('parcel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn('registration_number');
        });

        Schema::table('deeds', function (Blueprint $table) {

            $table->dropForeign(['promise_id']);
            $table->dropUnique(['promise_id']);
            $table->dropColumn('promise_id');


            $table->foreignId('parcel_id')
                ->nullable()
                ->unique()
                ->constrained('parcels')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
