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
            $table->float('fuel_level')->default(100)->after('status');
            $table->float('oxygen_level')->default(100)->after('fuel_level');
            $table->timestamp('last_maintenance_at')->nullable()->after('oxygen_level');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->string('unit')->default('units')->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn(['fuel_level', 'oxygen_level', 'last_maintenance_at']);
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};
