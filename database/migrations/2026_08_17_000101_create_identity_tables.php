<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Identity, authentication and audit.
 *
 * NOTE: replaces Laravel's default users migration.
 * Delete 0001_01_01_000000_create_users_table.php before running.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('slug', 40)->unique();      // founder|selected_oc|oc_member|member
            $t->string('name', 80);
            $t->unsignedTinyInteger('level');      // 40|30|20|10 — higher wins
            $t->json('permissions')->nullable();
            $t->timestamps();
        });

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('phone', 32)->nullable();
            $t->string('password');
            $t->foreignId('role_id')->constrained()->restrictOnDelete();
            $t->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $t->string('school')->nullable();
            $t->string('grade', 32)->nullable();
            $t->string('avatar_path')->nullable();

            // Rakhandar shared secret — hash only. Never plain text, never emailed.
            $t->string('rakhandar_pass_hash')->nullable();
            $t->timestamp('rakhandar_pass_set_at')->nullable();

            $t->timestamp('joined_at')->nullable();
            $t->timestamp('last_login_at')->nullable();
            $t->string('last_login_ip', 45)->nullable();
            $t->unsignedSmallInteger('failed_attempts')->default(0);
            $t->timestamp('locked_until')->nullable();
            $t->timestamp('email_verified_at')->nullable();
            $t->rememberToken();
            $t->timestamps();

            $t->index(['status', 'role_id']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $t) {
            $t->string('email')->primary();
            $t->string('token');
            $t->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });

        Schema::create('otp_codes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('purpose', ['rakhandar', 'password_reset', 'email_verify']);
            $t->string('code_hash');                  // hashed, never stored plain
            $t->timestamp('expires_at');              // 5 minutes
            $t->timestamp('consumed_at')->nullable(); // single use
            $t->unsignedTinyInteger('attempts')->default(0);
            $t->string('ip', 45)->nullable();
            $t->timestamps();

            $t->index(['user_id', 'purpose', 'consumed_at']);
        });

        Schema::create('access_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 80);                 // login|rakhandar_unlock|file_download|...
            $t->string('resource_type', 60)->nullable();
            $t->unsignedBigInteger('resource_id')->nullable();
            $t->enum('result', ['success', 'denied', 'error']);
            $t->string('ip', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->json('meta')->nullable();
            $t->timestamp('created_at')->useCurrent()->index();

            $t->index(['user_id', 'action']);
            $t->index(['result', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
        Schema::dropIfExists('otp_codes');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
