<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('citizen')->after('password');
        });

        Schema::create('admin_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('citizen_reports', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('location_name', 255);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('reported_at')->useCurrent();
            $table->string('media_path', 255)->nullable();
            $table->string('status', 30)->default('pending_admin');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();

            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizen_reports');
        Schema::dropIfExists('admin_settings');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
