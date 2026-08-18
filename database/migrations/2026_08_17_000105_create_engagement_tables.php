<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Streaks, reflections, credits and badges.
 * Original progress system — designed around consistency and reflection,
 * not ranking. Leaderboard is optional and off by default.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streak_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('activity_date');
            $t->enum('activity_type', ['research', 'build', 'documentation', 'meeting', 'presentation']);
            $t->unsignedSmallInteger('minutes');
            $t->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('evidence_media_id')->nullable()->constrained('media_items')->nullOnDelete();
            $t->text('note')->nullable();

            $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $t->unsignedSmallInteger('points_awarded')->default(0);
            $t->boolean('is_recovery')->default(false);   // missed-day recovery
            $t->timestamps();

            $t->index(['user_id', 'activity_date']);
            $t->index(['status', 'activity_date']);
        });

        // Denormalised so dashboards don't recompute streaks on every load
        Schema::create('user_streaks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('current_streak')->default(0);
            $t->unsignedSmallInteger('longest_streak')->default(0);
            $t->date('last_active_date')->nullable();
            $t->unsignedSmallInteger('weekly_goal_minutes')->default(120);
            $t->unsignedSmallInteger('week_minutes')->default(0);
            $t->unsignedInteger('total_points')->default(0);
            $t->unsignedTinyInteger('recoveries_left')->default(2);
            $t->timestamps();
        });

        Schema::create('reflections', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('streak_log_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->longText('body');
            $t->date('week_of')->nullable();
            $t->json('outcomes')->nullable();
            $t->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->text('feedback')->nullable();
            $t->timestamps();

            $t->index(['user_id', 'status']);
        });

        // Credits are NON-MONETARY. No payment system is active.
        Schema::create('credits', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->integer('balance')->default(0);
            $t->unsignedInteger('lifetime_earned')->default(0);
            $t->unsignedInteger('lifetime_spent')->default(0);
            $t->timestamps();
        });

        Schema::create('credit_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->integer('delta');                      // signed
            $t->integer('balance_after');
            $t->enum('category', ['award', 'deduction', 'redemption', 'adjustment']);
            $t->string('reason');                      // required
            $t->text('admin_note')->nullable();
            $t->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $t->string('reference_type', 60)->nullable();
            $t->unsignedBigInteger('reference_id')->nullable();
            $t->timestamps();

            $t->index(['user_id', 'created_at']);
        });

        Schema::create('badges', function (Blueprint $t) {
            $t->id();
            $t->string('slug', 60)->unique();
            $t->string('name', 80);
            $t->string('description', 300);
            $t->string('icon', 60)->nullable();
            $t->enum('category', ['consistency', 'teamwork', 'research', 'engineering',
                                  'documentation', 'presentation', 'innovation', 'leadership']);
            $t->json('criteria')->nullable();
            $t->timestamps();
        });

        Schema::create('badge_user', function (Blueprint $t) {
            $t->id();
            $t->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->text('note')->nullable();
            $t->timestamp('awarded_at');
            $t->timestamps();

            $t->unique(['badge_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('credit_transactions');
        Schema::dropIfExists('credits');
        Schema::dropIfExists('reflections');
        Schema::dropIfExists('user_streaks');
        Schema::dropIfExists('streak_logs');
    }
};
