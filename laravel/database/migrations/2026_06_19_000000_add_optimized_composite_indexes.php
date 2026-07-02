<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detection_logs', function (Blueprint $table) {
            $table->index(['created_at', 'confidence_score'], 'idx_dl_date_confidence');
            $table->index(['camera_id', 'label_id', 'created_at'], 'idx_dl_camera_label_date');
        });

        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->index(['location_name', 'status'], 'idx_cr_location_status');
        });
    }

    public function down(): void
    {
        Schema::table('detection_logs', function (Blueprint $table) {
            $table->dropIndex('idx_dl_date_confidence');
            $table->dropIndex('idx_dl_camera_label_date');
        });

        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->dropIndex('idx_cr_location_status');
        });
    }
};
