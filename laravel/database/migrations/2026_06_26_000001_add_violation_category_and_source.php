<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detection_logs', function (Blueprint $table) {
            $table->string('violation_category', 50)->nullable()->after('confidence_score');
            $table->index('violation_category');
        });

        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->string('source', 20)->default('public')->after('id');
            $table->string('violation_category', 50)->nullable()->after('location_name');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::table('detection_logs', function (Blueprint $table) {
            $table->dropIndex(['violation_category']);
            $table->dropColumn('violation_category');
        });

        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('violation_category');
            $table->dropColumn('source');
        });
    }
};
