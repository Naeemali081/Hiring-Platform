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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('problem_id');
            $table->string('candidate_name');
            $table->string('candidate_email');
            $table->enum('role_level', ['junior', 'mid', 'senior']);
            $table->string('token', 64)->unique();
            $table->unsignedInteger('time_limit_minutes');
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'reviewed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('defend_scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
