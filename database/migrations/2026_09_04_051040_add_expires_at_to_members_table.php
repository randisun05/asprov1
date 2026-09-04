<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('qr_link');
        });

        // One-time backfill: every member that already exists today gets
        // membership validity through the end of 2027, per the org's own
        // instruction, rather than defaulting to "created_at + 1 year"
        // (which would make long-time members expire almost immediately).
        // Members approved after this migration get "approved + 1 year"
        // instead - see Admin\RegistrationController::attemptApproval().
        DB::table('members')->whereNull('expires_at')->update([
            'expires_at' => '2027-12-31 23:59:59',
        ]);
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
