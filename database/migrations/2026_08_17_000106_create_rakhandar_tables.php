<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ULTIMATE RAKHANDAR — CONFIDENTIAL.
 *
 * Nothing in these tables is ever queried by a public route.
 * The public Rakhandar page reads curated static content and has no
 * code path to this data at all.
 *
 * Access requires ALL FOUR:
 *   1. approved logged-in account
 *   2. role is founder or selected_oc
 *   3. correct shared secret pass (per-user hash)
 *   4. single-use email OTP
 * ...then a 15-minute elevated session, re-checked on every page and download.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rakhandar_scans', function (Blueprint $t) {
            $t->id();
            $t->foreignId('monument_id')->constrained()->restrictOnDelete();
            $t->enum('scan_type', ['baseline', 'comparison']);
            $t->foreignId('baseline_scan_id')->nullable()
              ->constrained('rakhandar_scans')->nullOnDelete();

            $t->timestamp('scanned_at');
            $t->foreignId('operator_id')->constrained('users')->restrictOnDelete();
            $t->string('rover_ref', 60)->nullable();
            $t->unsignedTinyInteger('coverage_percent')->default(0);
            $t->enum('status', ['planned', 'in_progress', 'processing', 'complete', 'aborted'])
              ->default('planned');

            // Drone is CONDITIONAL and requires approval. Disabled by feature flag.
            $t->boolean('drone_requested')->default(false);
            $t->text('drone_request_reason')->nullable();
            $t->foreignId('drone_authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('drone_authorized_at')->nullable();
            $t->enum('drone_mode', ['manual', 'autonomous_360'])->nullable();

            $t->text('notes')->nullable();
            $t->timestamps();

            $t->index(['monument_id', 'scan_type', 'scanned_at']);
        });

        Schema::create('rakhandar_reports', function (Blueprint $t) {
            $t->id();
            $t->foreignId('scan_id')->constrained('rakhandar_scans')->cascadeOnDelete();
            $t->enum('type', ['inspection', 'comparison']);
            $t->string('title');
            $t->text('summary')->nullable();
            // {crack_count, new_cracks, thermal_anomalies, geometry_delta, ...}
            $t->json('findings')->nullable();
            $t->unsignedBigInteger('pdf_file_id')->nullable();
            $t->foreignId('generated_by')->constrained('users')->restrictOnDelete();
            $t->timestamp('published_at')->nullable();
            $t->timestamps();

            $t->index(['scan_id', 'type']);
        });

        Schema::create('rakhandar_files', function (Blueprint $t) {
            $t->id();
            $t->foreignId('scan_id')->nullable()->constrained('rakhandar_scans')->cascadeOnDelete();
            $t->foreignId('report_id')->nullable()->constrained('rakhandar_reports')->cascadeOnDelete();
            $t->enum('category', ['raw', 'point_cloud', 'digital_twin', 'thermal',
                                  'photo', 'pdf', 'field_log']);
            $t->string('path');                    // storage/app/private/rakhandar/** ONLY
            $t->string('original_name');
            $t->string('mime', 120)->nullable();
            $t->unsignedBigInteger('size')->nullable();
            $t->string('checksum', 64)->nullable();
            $t->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $t->enum('access_level', ['selected_oc', 'founder'])->default('selected_oc');
            $t->timestamps();

            $t->index(['scan_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rakhandar_files');
        Schema::dropIfExists('rakhandar_reports');
        Schema::dropIfExists('rakhandar_scans');
    }
};
