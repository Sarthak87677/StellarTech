<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email');
            $t->string('phone', 32)->nullable();
            $t->string('school')->nullable();
            $t->string('grade', 32)->nullable();
            $t->string('interest_area', 80);     // robotics|ai|space|engineering|heritage
            $t->text('motivation');
            $t->text('skills')->nullable();
            $t->string('project_preference')->nullable();
            $t->boolean('consent')->default(false);
            $t->boolean('parental_consent')->default(false);   // required if under 18

            $t->enum('status', ['pending', 'approved', 'rejected', 'waitlisted'])
              ->default('pending')->index();
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('reviewed_at')->nullable();
            $t->text('review_note')->nullable();
            $t->foreignId('created_user_id')->nullable()->constrained('users')->nullOnDelete();

            $t->string('ip', 45)->nullable();
            $t->timestamps();

            $t->index(['email', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
