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
        Schema::create('emergency_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->nullable()->index();
            $table->foreignId('hospital_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('ambulance_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'accepted', 'en_route', 'completed'])->default('pending');
            $table->string('emergency_type')->nullable();
            $table->enum('severity', ['low', 'medium', 'high'])->default('high');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_requests');
    }
};
