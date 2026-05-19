<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Medications Ecosystem
        Schema::create('medications', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('family_member_id')->nullable()->constrained('family_members')->onDelete('cascade');
            $blueprint->string('name');
            $blueprint->string('dosage')->nullable();
            $blueprint->string('timing')->nullable();
            $blueprint->string('duration')->nullable();
            $blueprint->string('purpose')->nullable();
            $blueprint->text('side_effects')->nullable();
            $blueprint->text('interaction_warnings')->nullable();
            $blueprint->date('start_date')->nullable();
            $blueprint->date('end_date')->nullable();
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamps();
        });

        // 2. Health Intelligence Snapshots (Versioning)
        Schema::create('health_intelligence_snapshots', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->string('type'); // score, risk, summary, trend
            $blueprint->json('data');
            $blueprint->integer('version')->default(1);
            $blueprint->timestamps();
            $blueprint->index(['user_id', 'type']);
        });

        // 3. Smart Health Alerts
        Schema::create('health_alerts', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->string('type');
            $blueprint->string('severity')->default('INFO'); // INFO, ADVISORY, IMPORTANT, HIGH_RISK, CRITICAL
            $blueprint->text('message');
            $blueprint->json('meta_data')->nullable();
            $blueprint->timestamp('read_at')->nullable();
            $blueprint->timestamps();
            $blueprint->index(['user_id', 'severity']);
        });

        // 4. Structured Health Memory (Indicators)
        Schema::create('health_indicators', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->string('key'); // bp, glucose, bmi, oxygen, etc.
            $blueprint->string('value');
            $blueprint->string('unit')->nullable();
            $blueprint->timestamp('measured_at');
            $blueprint->timestamps();
            $blueprint->index(['user_id', 'key']);
        });

        // 5. AI Recommendation Audit & Traceability
        Schema::create('ai_recommendations', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->text('content');
            $blueprint->string('type');
            $blueprint->float('confidence_score');
            $blueprint->string('model_used');
            $blueprint->text('reasoning_trace')->nullable();
            $blueprint->string('prompt_version')->nullable();
            $blueprint->foreignId('source_report_id')->nullable()->constrained('medical_reports')->onDelete('set null');
            $blueprint->timestamps();
        });

        // 6. Family Permissions & Relationships
        Schema::create('family_relationships', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('family_member_id')->constrained('family_members')->onDelete('cascade');
            $blueprint->string('relationship_type'); // guardian, dependent
            $blueprint->string('visibility_level')->default('emergency_only'); // full, emergency_only
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_relationships');
        Schema::dropIfExists('ai_recommendations');
        Schema::dropIfExists('health_indicators');
        Schema::dropIfExists('health_alerts');
        Schema::dropIfExists('health_intelligence_snapshots');
        Schema::dropIfExists('medications');
    }
};
