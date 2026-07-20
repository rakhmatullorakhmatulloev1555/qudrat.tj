<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showcase_projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');                              // Название (бренд, не переводится)
            $table->string('accent', 20)->default('#C9A96E');    // Акцентный цвет карточки/страницы
            $table->string('hero_image')->nullable();            // Главное изображение (hero + карточка)
            $table->json('gallery')->nullable();                 // Массив путей галереи
            $table->json('feature_icons')->nullable();           // Иконки особенностей (общие для языков)
            $table->json('content')->nullable();                 // Переводимый контент: {ru:{...},tj:{...},en:{...}}
            $table->json('seo')->nullable();                     // SEO: meta/OG (per-locale), canonical, schema
            $table->string('cta_type', 20)->default('apts');     // apts | contact | mining
            $table->string('status', 20)->default('draft');      // draft | published | archived
            $table->boolean('is_featured')->default(true);       // Показывать в блоке «Знаковые объекты»
            $table->boolean('is_visible')->default(true);        // Видимость в списках/блоках
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_visible', 'is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showcase_projects');
    }
};
