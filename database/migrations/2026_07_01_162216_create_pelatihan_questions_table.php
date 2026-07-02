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
        Schema::create('pelatihan_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_id')->constrained('pelatihans')->cascadeOnDelete();
            $table->string('tipe')->default('pilihan_ganda');
            $table->text('pertanyaan');
            $table->boolean('correct_answer')->nullable();
            $table->text('kunci_jawaban')->nullable();
            $table->unsignedInteger('bobot')->default(1);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihan_questions');
    }
};
