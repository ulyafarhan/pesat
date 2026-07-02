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
        Schema::table('cameras', function (Blueprint $table) {
            $table->string('edge_device_id', 100)->nullable()->after('stream_source');
            $table->timestamp('last_heartbeat_at')->nullable()->after('edge_device_id');
            $table->json('edge_metrics')->nullable()->after('last_heartbeat_at');
            $table->index(['edge_device_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cameras', function (Blueprint $table) {
            $table->dropIndex(['edge_device_id', 'is_active']);
            $table->dropColumn(['edge_device_id', 'last_heartbeat_at', 'edge_metrics']);
        });
    }
};
