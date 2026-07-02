<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detection_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        Schema::table('detection_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('label_id')->nullable()->after('camera_id');
        });

        $logs = DB::table('detection_logs')->select('id', 'label_detected')->get();
        foreach ($logs as $log) {
            $labelId = DB::table('detection_labels')->where('name', $log->label_detected)->value('id');
            if (! $labelId) {
                $labelId = DB::table('detection_labels')->insertGetId([
                    'name' => $log->label_detected,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('detection_logs')->where('id', $log->id)->update(['label_id' => $labelId]);
        }

        Schema::table('detection_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('label_id')->nullable(false)->change();
            $table->foreign('label_id')->references('id')->on('detection_labels')->onDelete('cascade');

            $table->index(['camera_id', 'created_at']);
            $table->index(['label_id']);
        });

        Schema::table('detection_logs', function (Blueprint $table) {
            $table->dropColumn('label_detected');
        });
    }

    public function down(): void
    {
        Schema::table('detection_logs', function (Blueprint $table) {
            $table->string('label_detected', 100)->nullable()->after('camera_id');
        });

        $logs = DB::table('detection_logs')->get();
        foreach ($logs as $log) {
            if ($log->label_id) {
                $labelName = DB::table('detection_labels')->where('id', $log->label_id)->value('name');
                DB::table('detection_logs')->where('id', $log->id)->update(['label_detected' => substr($labelName, 0, 100)]);
            }
        }

        Schema::table('detection_logs', function (Blueprint $table) {
            $table->string('label_detected', 100)->nullable(false)->change();
            $table->dropForeign(['label_id']);
            $table->dropIndex(['camera_id', 'created_at']);
            $table->dropIndex(['label_id']);
            $table->dropColumn('label_id');
        });

        Schema::dropIfExists('detection_labels');
    }
};
