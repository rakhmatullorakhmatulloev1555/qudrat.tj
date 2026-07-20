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
        Schema::create('construction_updates', function (Blueprint $table) {
            $table->id();
            $table->date('update_date');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('progress')->default(0); // 0–100 %
            $table->boolean('is_current')->default(false);       // Latest active update
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Seed initial record so the page works immediately
        \DB::table('construction_updates')->insert([
            'update_date'  => '2026-05-20',
            'title'        => 'Текущий прогресс строительства',
            'description'  => 'Завершён нулевой цикл, ведётся возведение монолитного каркаса.',
            'progress'     => 34,
            'is_current'   => true,
            'is_published' => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('construction_updates');
    }
};
