<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // One row per published item (event/tryout/post/merchan/announcement),
        // broadcast to every member - not one row per member per item, which
        // would multiply inserts by the member count on every publish.
        // "Read" state is tracked separately in member_notification_reads.
        Schema::create('member_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('link');
            $table->nullableMorphs('notifiable');
            $table->timestamps();
        });

        Schema::create('member_notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_notification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->unique(['member_notification_id', 'member_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_notification_reads');
        Schema::dropIfExists('member_notifications');
    }
};
