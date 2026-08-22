<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Certificate numbering (no_certificate) was computed by reading the
     * highest existing number for a category/period and adding one, with
     * no row lock and no unique constraint on no_certificate to catch a
     * collision afterward. Two concurrent certificate creations for the
     * same category/period can silently produce the same certificate
     * number - worse than the equivalent member-numbering bug, which at
     * least had a unique constraint as a backstop.
     *
     * One row per numbering scope (namespaced per call site + category +
     * period, matching each site's existing reset boundary exactly),
     * locked by exact key match before incrementing.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_number_sequences', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_number_sequences');
    }
};
