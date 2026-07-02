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
        Schema::table('detection_logs', function (Blueprint $table) {
            $table->string('snapshot', 100)->nullable()->after('violation_category');
        });
    }

    public function down(): void
    {
        Schema::table('detection_logs', function (Blueprint $table) {
            $table->dropColumn('snapshot');
        });
    }
};
