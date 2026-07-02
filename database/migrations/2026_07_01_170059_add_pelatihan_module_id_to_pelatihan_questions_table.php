<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelatihan_questions', function (Blueprint $table) {
            $table->foreignId('pelatihan_module_id')->nullable()->after('pelatihan_id')
                ->constrained('pelatihan_modules')->cascadeOnDelete();
        });

        // Backfill: setiap pelatihan yang sudah punya soal dibuatkan satu modul
        // default "Materi Utama", lalu semua soalnya dipindah ke modul tersebut.
        $pelatihanIds = DB::table('pelatihan_questions')->distinct()->pluck('pelatihan_id');

        foreach ($pelatihanIds as $pelatihanId) {
            $moduleId = DB::table('pelatihan_modules')->insertGetId([
                'pelatihan_id' => $pelatihanId,
                'title' => 'Materi Utama',
                'urutan' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pelatihan_questions')
                ->where('pelatihan_id', $pelatihanId)
                ->update(['pelatihan_module_id' => $moduleId]);
        }

        Schema::table('pelatihan_questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pelatihan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelatihan_questions', function (Blueprint $table) {
            $table->foreignId('pelatihan_id')->nullable()->after('id')
                ->constrained('pelatihans')->cascadeOnDelete();
        });

        foreach (DB::table('pelatihan_questions')->whereNotNull('pelatihan_module_id')->get() as $question) {
            $module = DB::table('pelatihan_modules')->find($question->pelatihan_module_id);

            if ($module) {
                DB::table('pelatihan_questions')
                    ->where('id', $question->id)
                    ->update(['pelatihan_id' => $module->pelatihan_id]);
            }
        }

        Schema::table('pelatihan_questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pelatihan_module_id');
        });
    }
};
