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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->longText('code');
            $table->string('language', 50)->default('php');
            $table->json('execution_result')->nullable();
            $table->unsignedInteger('manual_chars')->default(0);
            $table->unsignedInteger('pasted_chars')->default(0);
            $table->unsignedInteger('paste_events')->default(0);
            $table->unsignedInteger('tab_switches')->default(0);
            $table->json('tab_switch_timestamps')->nullable();
            $table->json('keystrokes_log')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->text('reviewer_notes')->nullable();
            $table->enum('defend_outcome', ['pass', 'fail', 'undecided'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
