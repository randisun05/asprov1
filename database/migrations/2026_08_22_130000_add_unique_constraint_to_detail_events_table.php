<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Guards against the enroll race (a double-submitted "daftarkan member"
     * could previously create two detail_events rows for the same
     * event+member) with a DB-level backstop, matching the unique
     * constraint answers already has. If duplicate rows already exist in
     * this database, this migration will fail loudly instead of silently
     * dropping data — resolve the duplicates manually before re-running.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_events', function (Blueprint $table) {
            $table->unique(['event_id', 'member_id'], 'unique_event_member');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_events', function (Blueprint $table) {
            $table->dropUnique('unique_event_member');
        });
    }
};
