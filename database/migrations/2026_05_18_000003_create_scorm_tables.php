<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scorm_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')->constrained('modules')->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('version')->default('1.2');
            $table->string('zip_path');
            $table->string('extract_path');
            $table->string('launch_path');
            $table->json('manifest')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('scorm_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scorm_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('lesson_status')->default('not attempted');
            $table->string('completion_status')->default('unknown');
            $table->decimal('progress', 5, 2)->default(0);
            $table->decimal('score_raw', 6, 2)->nullable();
            $table->decimal('score_min', 6, 2)->nullable();
            $table->decimal('score_max', 6, 2)->nullable();
            $table->string('session_time')->nullable();
            $table->string('total_time')->nullable();
            $table->longText('suspend_data')->nullable();
            $table->string('last_location')->nullable();
            $table->json('runtime_data')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            $table->unique(['scorm_package_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scorm_tracks');
        Schema::dropIfExists('scorm_packages');
    }
};
