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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['residential', 'commercial', 'mixed'])->default('residential');
            $table->enum('status', ['planned', 'under_construction', 'on_sale', 'completed'])->default('planned');
            $table->enum('class', ['economy', 'comfort', 'business', 'premium'])->default('comfort');
            $table->string('city')->default('Душанбе');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price_from', 15, 2)->nullable();
            $table->decimal('price_to', 15, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->integer('floors_count')->nullable();
            $table->integer('apartments_count')->nullable();
            $table->integer('completion_year')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
