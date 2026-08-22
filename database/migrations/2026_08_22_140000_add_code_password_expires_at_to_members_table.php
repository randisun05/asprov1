<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The forgot-password reset code ('code-password') never expired, so a
     * leaked reset link (old email, browser history) stayed valid forever
     * until the next reset request overwrote it. This adds an expiry
     * timestamp so ResetPassword/IndexforgetPassword can reject stale links.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->timestamp('code_password_expires_at')->nullable()->after('code-password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('code_password_expires_at');
        });
    }
};
