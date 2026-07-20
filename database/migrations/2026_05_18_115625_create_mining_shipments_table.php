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
        Schema::create('mining_shipments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('site');
            $table->enum('coal_type', ['energy', 'coking', 'anthracite'])->default('energy');
            $table->decimal('volume', 10, 2);
            $table->decimal('price_per_ton', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('buyer')->nullable();
            $table->string('destination')->nullable();
            $table->enum('status', ['planned', 'loading', 'shipped', 'delivered', 'paid'])->default('planned');
            $table->integer('quality_kcal')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mining_shipments');
    }
};
