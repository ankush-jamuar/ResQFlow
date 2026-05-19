<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitals', 'warning_points')) {
                $table->integer('warning_points')->default(0)->after('available_beds');
            }
            if (!Schema::hasColumn('hospitals', 'reliability_score')) {
                $table->decimal('reliability_score', 4, 1)->default(100.0)->after('warning_points');
            }
            if (!Schema::hasColumn('hospitals', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false)->after('reliability_score');
            }
            if (!Schema::hasColumn('hospitals', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('is_suspended');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['warning_points', 'reliability_score', 'is_suspended', 'admin_notes']);
        });
    }
};
