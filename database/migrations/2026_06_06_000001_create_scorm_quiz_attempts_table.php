<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scorm_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scorm_track_id')->constrained('scorm_tracks')->cascadeOnDelete();
            $table->foreignId('scorm_package_id')->constrained('scorm_packages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('answers')->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->string('status')->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique('scorm_track_id');
            $table->index(['scorm_package_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scorm_quiz_attempts');
    }
};
