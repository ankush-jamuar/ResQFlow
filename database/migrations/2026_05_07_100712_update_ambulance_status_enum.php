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
            $table->enum('status', ['available', 'dispatched', 'in_transit', 'maintenance'])->default('available')->change();
        });
    }

    public function down(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            $table->enum('status', ['available', 'dispatched'])->default('available')->change();
        });
    }
};
