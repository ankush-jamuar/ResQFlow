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
        Schema::table('ai_recommendations', function (Blueprint $table) {
            $table->text('rationale')->nullable()->after('content');
            $table->json('supporting_evidence')->nullable()->after('rationale');
            $table->string('stability_hash')->nullable()->after('supporting_evidence');
            $table->foreignId('parent_recommendation_id')->nullable()->after('stability_hash')->constrained('ai_recommendations')->onDelete('set null');
            $table->boolean('is_acknowledged')->default(false)->after('parent_recommendation_id');
            $table->string('confidence_language')->nullable()->after('confidence_score');
        });

        Schema::table('health_indicators', function (Blueprint $table) {
            $table->float('confidence_score')->default(1.0)->after('unit');
            $table->string('source_type')->default('user')->after('confidence_score'); // user, ai_extraction, device
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_indicators', function (Blueprint $table) {
            $table->dropColumn(['confidence_score', 'source_type']);
        });

        Schema::table('ai_recommendations', function (Blueprint $table) {
            $table->dropForeign(['parent_recommendation_id']);
            $table->dropColumn([
                'rationale', 
                'supporting_evidence', 
                'stability_hash', 
                'parent_recommendation_id', 
                'is_acknowledged',
                'confidence_language'
            ]);
        });
    }
};
