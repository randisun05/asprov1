<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['event_id', 'question_id']);
        });

        // Backfill: soal yang sudah ada masih terikat langsung ke event_id,
        // salin relasinya ke pivot supaya tryout lama tetap punya soalnya.
        $now = now();
        DB::table('questions')
            ->whereNotNull('event_id')
            ->select('id', 'event_id')
            ->orderBy('id')
            ->chunk(500, function ($questions) use ($now) {
                $rows = $questions->map(fn ($question) => [
                    'event_id' => $question->event_id,
                    'question_id' => $question->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                DB::table('event_question')->insertOrIgnore($rows);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_question');
    }
};
