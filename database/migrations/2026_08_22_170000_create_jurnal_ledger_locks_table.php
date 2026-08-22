<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A single-row lock table. Every jurnal-mutating action (store/update/
     * destroy) locks this row inside a DB transaction before touching the
     * `nomor` sequence or the running `saldo`, so concurrent requests are
     * serialized instead of racing on Jurnal::latest()->first().
     */
    public function up()
    {
        Schema::create('jurnal_ledger_locks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        DB::table('jurnal_ledger_locks')->insert([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('jurnal_ledger_locks');
    }
};
