<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cameras', function (Blueprint $table) {
            $table->index(['is_active'], 'idx_cameras_is_active');
        });

        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->index(['status', 'updated_at'], 'idx_citizen_reports_status_updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->dropIndex('idx_citizen_reports_status_updated_at');
        });

        Schema::table('cameras', function (Blueprint $table) {
            $table->dropIndex('idx_cameras_is_active');
        });
    }
};
