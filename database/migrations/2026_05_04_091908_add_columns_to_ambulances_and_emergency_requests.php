<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add in_transit status to ambulances enum (MySQL requires raw ALTER for enum changes)
        DB::statement("ALTER TABLE ambulances MODIFY COLUMN status ENUM('available', 'dispatched', 'in_transit') NOT NULL DEFAULT 'available'");

        // Add plate_number to ambulances (referenced in track() response but was missing from schema)
        if (!Schema::hasColumn('ambulances', 'plate_number')) {
            Schema::table('ambulances', function (Blueprint $table) {
                $table->string('plate_number')->nullable()->after('driver_name');
            });
        }

        // Add completed_at timestamp to emergency_requests for lifecycle tracking
        Schema::table('emergency_requests', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ambulances MODIFY COLUMN status ENUM('available', 'dispatched') NOT NULL DEFAULT 'available'");

        if (Schema::hasColumn('ambulances', 'plate_number')) {
            Schema::table('ambulances', function (Blueprint $table) {
                $table->dropColumn('plate_number');
            });
        }

        Schema::table('emergency_requests', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
