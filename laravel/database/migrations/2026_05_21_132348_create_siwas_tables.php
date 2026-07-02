<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cameras', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('location_name', 150);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->boolean('is_active')->default(true);
            $table->string('stream_source', 255)->default('0');
            $table->timestamps();
        });

        Schema::create('detection_logs', function (Blueprint $table) {
            $table->id();
            $table->string('camera_id', 50);
            $table->string('label_detected', 50);
            $table->decimal('confidence_score', 4, 3);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('camera_id')->references('id')->on('cameras')->onDelete('cascade');
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detection_logs');
        Schema::dropIfExists('cameras');
    }
};
