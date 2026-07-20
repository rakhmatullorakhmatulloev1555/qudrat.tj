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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            // call|email|note|task|meeting|whatsapp|telegram|status_change
            $table->string('type', 30);
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('outcome')->nullable(); // answered|no_answer|callback|interested...
            // Polymorphic: Lead, Client, Deal
            $table->string('related_type');
            $table->unsignedBigInteger('related_id');
            $table->unsignedBigInteger('user_id')->nullable(); // who logged it
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('duration_seconds')->nullable();
            $table->enum('direction', ['in','out','internal'])->default('out');
            $table->boolean('is_done')->default(false);
            $table->json('attachments')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['related_type','related_id']);
            $table->index(['user_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
