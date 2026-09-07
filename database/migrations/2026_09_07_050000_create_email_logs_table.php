<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('mailable');
            $table->string('type');
            $table->string('to_email');
            $table->string('status')->default('pending');
            $table->text('error')->nullable();
            $table->nullableMorphs('notifiable');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_logs');
    }
};
