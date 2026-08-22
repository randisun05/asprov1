<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Member number generation used to lock rows found via
     * `Member::where('nomember', 'LIKE', '%'.$suffix)` - a leading wildcard
     * that can't use the unique index on nomember, so it full-table-scans
     * (and locks across) the entire members table on every approval, and
     * has nothing to lock at all for the very first member of a suffix
     * (only caught after the fact by a unique-constraint retry). Both make
     * concurrent approvals collide/block far more than necessary.
     *
     * This gives each suffix ('/01/ASPROSDMA', '/02/ASPROSDMA',
     * '/LB/ASPROSDMA', ...) its own row, locked by exact primary-key
     * match - a single indexed row per suffix instead of a scan of the
     * whole table, and it always exists so there's no "first member"
     * phantom-lock gap.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_number_sequences', function (Blueprint $table) {
            $table->string('suffix')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        // Seed each known suffix from the highest number already issued in
        // `members`, so the new sequence continues where the old ad-hoc
        // scan left off instead of colliding with real historical data.
        $now = now();
        $suffixes = ['/01/ASPROSDMA', '/02/ASPROSDMA', '/LB/ASPROSDMA'];

        foreach ($suffixes as $suffix) {
            $members = DB::table('members')
                ->where('nomember', 'like', '%' . $suffix)
                ->pluck('nomember');

            $lastNumber = 0;
            foreach ($members as $nomember) {
                $prefix = strstr($nomember, '/', true) ?: $nomember;
                $lastNumber = max($lastNumber, (int) $prefix);
            }

            DB::table('member_number_sequences')->insert([
                'suffix' => $suffix,
                'last_number' => $lastNumber,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_number_sequences');
    }
};
