<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Галерея фотографий квартиры (несколько фото на квартиру).
 * plan_image (планировка) остаётся в apartments — это отдельная сущность.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartment_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('apartment_id')->constrained('apartments')->cascadeOnDelete();
            $t->string('path');
            $t->string('alt')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('is_primary')->default(false);
            $t->timestamps();
            $t->index(['apartment_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartment_images');
    }
};
