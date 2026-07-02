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
        Schema::create('pelatihan_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_question_id')->constrained('pelatihan_questions')->cascadeOnDelete();
            $table->string('teks_pilihan');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihan_question_options');
    }
};
