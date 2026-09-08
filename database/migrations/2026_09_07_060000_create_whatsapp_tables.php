<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Single-row settings: an admin-controlled on/off switch plus the
        // Fonnte device token, editable from the admin UI without touching
        // .env or redeploying - unlike every other 3rd-party credential in
        // this app (Midtrans, reCAPTCHA), which is .env-only.
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->string('api_token')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('to_phone');
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
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('whatsapp_settings');
    }
};
