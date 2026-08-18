<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Media pipeline:
 *   upload -> private disk, status=pending
 *   -> review -> approved + visibility set
 *   -> if public, published to the public disk and eligible for the homepage carousel
 *
 * Only status=approved AND visibility=public ever renders to visitors.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('uploader_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $t->enum('type', ['image', 'video', 'document', 'embed']);

            $t->string('path');                       // private disk until approved
            $t->string('public_path')->nullable();    // set only on public approval
            $t->string('thumb_path')->nullable();
            $t->string('embed_url')->nullable();      // YouTube/Vimeo — saves disk quota
            $t->string('mime', 120)->nullable();
            $t->unsignedBigInteger('size')->nullable();
            $t->unsignedInteger('duration')->nullable();
            $t->string('checksum', 64)->nullable();

            $t->string('caption', 500)->nullable();
            $t->date('captured_at')->nullable();
            $t->json('tags')->nullable();
            $t->string('team_names')->nullable();

            $t->enum('visibility', ['public', 'members', 'private'])->default('private');
            $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $t->boolean('is_featured_home')->default(false);
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->boolean('exif_stripped')->default(false);   // GPS removed before publishing
            $t->timestamps();

            $t->index(['status', 'visibility', 'is_featured_home']);
        });

        Schema::create('media_approvals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('media_id')->constrained('media_items')->cascadeOnDelete();
            $t->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $t->enum('decision', ['approved', 'rejected', 'changes_requested']);
            $t->text('note')->nullable();
            $t->timestamp('decided_at');
            $t->timestamps();

            $t->index(['media_id', 'decided_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_approvals');
        Schema::dropIfExists('media_items');
    }
};
