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
        Schema::create('pelatihan_module_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_module_id')->constrained('pelatihan_modules')->cascadeOnDelete();
            $table->string('title');
            $table->string('source_type')->default('embed');
            $table->string('url')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihan_module_videos');
    }
};
