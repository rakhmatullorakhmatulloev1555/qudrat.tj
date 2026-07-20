<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Схема системы «3D Квартиры» (Этап 2).
 *
 * Покрывает все разделы админки:
 *   Стили → interior_styles
 *   Квартиры (сцены) → apartment_scenes
 *   Комнаты → scene_rooms
 *   3D модели / Текстуры / HDRI → scene_assets (единая таблица по kind — DRY)
 *   Материалы → materials (переиспользуемая библиотека)
 *   Маршруты камеры → camera_routes
 *   Hotspots → scene_hotspots
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Стили интерьера (глобальные, переиспользуемые) ──
        Schema::create('interior_styles', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('name');
            $t->string('accent_hex', 9)->nullable();
            $t->string('background_hex', 9)->nullable();
            $t->json('materials')->nullable();   // конфиг материалов (wall/floor/…)
            $t->json('lighting')->nullable();     // ambient/hemi/dir/exposure…
            $t->string('hdri_path')->nullable();  // опциональный HDRI для стиля
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
        });

        // ── Сцена квартиры (1:1 с apartments) ──
        Schema::create('apartment_scenes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('apartment_id')->unique()->constrained('apartments')->cascadeOnDelete();
            $t->boolean('is_enabled')->default(false);
            $t->string('default_style_slug')->nullable();
            $t->decimal('ceiling_height', 4, 2)->nullable();
            $t->json('config')->nullable();
            $t->timestamps();
        });

        // ── Комнаты сцены ──
        Schema::create('scene_rooms', function (Blueprint $t) {
            $t->id();
            $t->foreignId('apartment_scene_id')->constrained('apartment_scenes')->cascadeOnDelete();
            $t->string('slug');            // living | kitchen | bedroom | bathroom | balcony | hall | storage
            $t->string('name');
            $t->string('icon')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->json('camera_pos')->nullable();     // [x,y,z]
            $t->json('camera_target')->nullable();  // [x,y,z]
            $t->unsignedBigInteger('model_asset_id')->nullable()->index();
            $t->json('config')->nullable();
            $t->timestamps();
            $t->index(['apartment_scene_id', 'slug']);
        });

        // ── Ассеты: модели, текстуры, HDRI, карты материалов ──
        Schema::create('scene_assets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('apartment_scene_id')->nullable()->constrained('apartment_scenes')->cascadeOnDelete();
            $t->unsignedBigInteger('interior_style_id')->nullable()->index();
            $t->unsignedBigInteger('scene_room_id')->nullable()->index();
            $t->string('kind');            // model | texture | hdri | material_map
            $t->string('name');
            $t->string('path');
            $t->string('disk')->default('public');
            $t->string('format')->nullable();       // glb|gltf|hdr|jpg|png|webp|ktx2
            $t->unsignedBigInteger('size_bytes')->default(0);
            $t->string('status')->default('uploaded'); // uploaded|processing|ready|error
            $t->json('meta')->nullable();           // resolution, draco, validation report…
            $t->timestamps();
            $t->index(['apartment_scene_id', 'kind']);
        });

        // ── Библиотека материалов (переиспользуемая) ──
        Schema::create('materials', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('name');
            $t->string('type')->nullable();  // marble|parquet|tile|wood|metal|glass|gold|brass
            $t->json('params')->nullable();  // color/roughness/metalness/map refs
            $t->string('preview_path')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        // ── Маршруты камеры (кинематографические туры) ──
        Schema::create('camera_routes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('apartment_scene_id')->constrained('apartment_scenes')->cascadeOnDelete();
            $t->string('name');
            $t->boolean('is_default')->default(false);
            $t->json('waypoints')->nullable();  // [{pos,target,duration,ease}]
            $t->timestamps();
        });

        // ── Hotspots (интерактивные точки) ──
        Schema::create('scene_hotspots', function (Blueprint $t) {
            $t->id();
            $t->foreignId('apartment_scene_id')->constrained('apartment_scenes')->cascadeOnDelete();
            $t->unsignedBigInteger('scene_room_id')->nullable()->index();
            $t->string('name');
            $t->text('description')->nullable();
            $t->json('position')->nullable();    // [x,y,z]
            $t->string('icon')->nullable();
            $t->string('target_room_slug')->nullable(); // переход по клику
            $t->json('meta')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scene_hotspots');
        Schema::dropIfExists('camera_routes');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('scene_assets');
        Schema::dropIfExists('scene_rooms');
        Schema::dropIfExists('apartment_scenes');
        Schema::dropIfExists('interior_styles');
    }
};
