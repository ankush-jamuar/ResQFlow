<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expand emergency_requests status ENUM to include dispatched + arrived
        DB::statement("ALTER TABLE emergency_requests MODIFY COLUMN status ENUM(
            'pending','accepted','dispatched','en_route','arrived','completed','cancelled','rejected'
        ) NOT NULL DEFAULT 'pending'");

        // System settings table for operationalized admin settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('boolean'); // boolean | integer | string
            $table->timestamps();
        });

        // Seed defaults
        DB::table('system_settings')->insert([
            ['key' => 'ai_routing_enabled',      'value' => '1', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'high_priority_intercept', 'value' => '0', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_dispatch_radius_km',  'value' => '50','type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'unit_timeout_seconds',    'value' => '60','type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'push_notifications',      'value' => '1', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'realtime_dashboards',     'value' => '1', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        // Revert status ENUM
        DB::statement("ALTER TABLE emergency_requests MODIFY COLUMN status ENUM(
            'pending','accepted','en_route','completed','cancelled','rejected'
        ) NOT NULL DEFAULT 'pending'");

        Schema::dropIfExists('system_settings');
    }
};
