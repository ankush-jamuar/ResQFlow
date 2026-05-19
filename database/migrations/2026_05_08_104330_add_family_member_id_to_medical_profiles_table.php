<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('family_member_id')->nullable()->after('user_id')->constrained('family_members')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('medical_profiles', function (Blueprint $table) {
            $table->dropForeign(['family_member_id']);
            $table->dropColumn('family_member_id');
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
