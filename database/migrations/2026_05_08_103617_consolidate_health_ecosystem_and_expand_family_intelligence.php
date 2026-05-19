<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Expand Medical Reports for Lifecycle Maturity
        Schema::table('medical_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_reports', 'family_member_id')) {
                $table->foreignId('family_member_id')->nullable()->after('user_id')->constrained('family_members')->onDelete('cascade');
            }
            if (!Schema::hasColumn('medical_reports', 'error_reason')) {
                $table->text('error_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('medical_reports', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('error_reason');
            }
            if (!Schema::hasColumn('medical_reports', 'extraction_metadata')) {
                $table->json('extraction_metadata')->nullable()->after('parsed_data');
            }
        });

        // 2. Add family_member_id to Health Intelligence Tables
        Schema::table('health_indicators', function (Blueprint $table) {
            if (!Schema::hasColumn('health_indicators', 'family_member_id')) {
                $table->foreignId('family_member_id')->nullable()->after('user_id')->constrained('family_members')->onDelete('cascade');
            }
        });

        Schema::table('health_alerts', function (Blueprint $table) {
            if (!Schema::hasColumn('health_alerts', 'family_member_id')) {
                $table->foreignId('family_member_id')->nullable()->after('user_id')->constrained('family_members')->onDelete('cascade');
            }
        });

        Schema::table('ai_recommendations', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_recommendations', 'family_member_id')) {
                $table->foreignId('family_member_id')->nullable()->after('user_id')->constrained('family_members')->onDelete('cascade');
            }
        });

        Schema::table('health_intelligence_snapshots', function (Blueprint $table) {
            if (!Schema::hasColumn('health_intelligence_snapshots', 'family_member_id')) {
                $table->foreignId('family_member_id')->nullable()->after('user_id')->constrained('family_members')->onDelete('cascade');
            }
        });

        // 3. Update Medical Profiles for Auto-Sync
        Schema::table('medical_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_profiles', 'is_manual_verified')) {
                $table->boolean('is_manual_verified')->default(false)->after('height_cm');
            }
            if (!Schema::hasColumn('medical_profiles', 'last_ai_sync_at')) {
                $table->timestamp('last_ai_sync_at')->nullable()->after('is_manual_verified');
            }
            if (!Schema::hasColumn('medical_profiles', 'sync_source')) {
                $table->string('sync_source')->nullable()->after('last_ai_sync_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_profiles', function (Blueprint $table) {
            $table->dropColumn(['is_manual_verified', 'last_ai_sync_at', 'sync_source']);
        });

        Schema::table('health_intelligence_snapshots', function (Blueprint $table) {
            $table->dropForeign(['family_member_id']);
            $table->dropColumn('family_member_id');
        });

        Schema::table('ai_recommendations', function (Blueprint $table) {
            $table->dropForeign(['family_member_id']);
            $table->dropColumn('family_member_id');
        });

        Schema::table('health_alerts', function (Blueprint $table) {
            $table->dropForeign(['family_member_id']);
            $table->dropColumn('family_member_id');
        });

        Schema::table('health_indicators', function (Blueprint $table) {
            $table->dropForeign(['family_member_id']);
            $table->dropColumn('family_member_id');
        });

        Schema::table('medical_reports', function (Blueprint $table) {
            $table->dropForeign(['family_member_id']);
            $table->dropColumn(['family_member_id', 'error_reason', 'processed_at', 'extraction_metadata']);
        });
    }
};
