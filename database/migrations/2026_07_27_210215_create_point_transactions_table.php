<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('type', ['reward', 'redeem']);
            $table->unsignedInteger('amount');
            $table->integer('balance_after');
            $table->string('description')->nullable();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
