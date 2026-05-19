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
        Schema::table('ambulances', function (Blueprint $table) {
            $table->decimal('current_latitude', 10, 8)->nullable()->after('current_location');
            $table->decimal('current_longitude', 11, 8)->nullable()->after('current_latitude');
        });

        Schema::table('emergency_requests', function (Blueprint $table) {
            $table->integer('eta_minutes')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn(['current_latitude', 'current_longitude']);
        });

        Schema::table('emergency_requests', function (Blueprint $table) {
            $table->dropColumn('eta_minutes');
        });
    }
};
