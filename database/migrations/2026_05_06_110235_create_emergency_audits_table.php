<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // The user who performed the action (admin/hospital)
            $table->string('action'); // e.g., 'status_change', 'reassigned', 'warned', 'suspended', 'cancelled'
            $table->string('old_state')->nullable();
            $table->string('new_state')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_audits');
    }
};
