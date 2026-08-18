<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title');
            $t->string('summary', 500);
            $t->longText('description')->nullable();
            $t->enum('discipline', ['robotics', 'ai', 'space', 'engineering', 'heritage']);
            // Honest stage labels — surfaced publicly as chips
            $t->enum('stage', ['planned', 'prototype', 'testing', 'active', 'completed'])
              ->default('planned');
            $t->enum('visibility', ['public', 'members', 'private'])->default('private');
            $t->foreignId('lead_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->unsignedBigInteger('cover_media_id')->nullable();
            $t->date('started_at')->nullable();
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();

            $t->index(['visibility', 'discipline']);
        });

        Schema::create('project_updates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $t->string('title');
            $t->longText('body');
            $t->date('update_date');
            $t->enum('visibility', ['public', 'members', 'private'])->default('members');
            $t->boolean('is_approved')->default(false);
            $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();

            $t->index(['project_id', 'visibility', 'is_approved']);
        });

        Schema::create('tasks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->text('description')->nullable();
            $t->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $t->enum('status', ['backlog', 'todo', 'in_progress', 'review', 'done'])
              ->default('backlog')->index();
            $t->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $t->date('due_date')->nullable();
            $t->unsignedSmallInteger('points')->default(0);
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();

            $t->index(['assigned_to', 'status']);
        });

        // Comparison scans key off a real monument record, not a free-text string.
        // Location data is PRIVATE — never exposed on the public site.
        Schema::create('monuments', function (Blueprint $t) {
            $t->id();
            $t->string('code', 32)->unique();            // internal reference
            $t->string('name');
            $t->string('region')->nullable();
            $t->text('location_note')->nullable();       // PRIVATE
            $t->decimal('latitude', 10, 7)->nullable();  // PRIVATE
            $t->decimal('longitude', 10, 7)->nullable(); // PRIVATE
            $t->text('permissions_note')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monuments');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('project_updates');
        Schema::dropIfExists('projects');
    }
};
