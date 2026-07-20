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
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('number', 20);
            $table->integer('floor');
            $table->integer('rooms');
            $table->decimal('area', 8, 2);
            $table->decimal('price', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['available', 'reserved', 'sold'])->default('available');
            $table->enum('finish', ['none', 'rough', 'fine', 'furnished'])->default('rough');
            $table->string('plan_image')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
