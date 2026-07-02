<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->boolean('is_break_dispatch')->default(false)->after('media_path');
        });

        // Convert existing statuses to normalized statuses
        DB::table('citizen_reports')
            ->whereIn('status', ['pending_admin', 'pending_wh'])
            ->update(['status' => 'pending']);

        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->string('status', 30)->default('pending_admin')->change();
            $table->dropColumn('is_break_dispatch');
        });
    }
};
