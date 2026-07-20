<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Интерактивный выбор квартиры: Генплан → Корпус → Этаж → Квартира.
 * Полигоны хранятся в нормализованных координатах (0-1000 x 0-1000 viewBox),
 * поэтому не зависят от фактического размера изображения.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Корпуса (blocks) ──
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');                        // «Блок A»
            $table->string('slug');                        // block-a
            $table->unsignedSmallInteger('floors_total')->default(1);
            $table->string('facade_image')->nullable();    // фасад для выбора этажа
            $table->json('masterplan_polygon')->nullable();// полигон на генплане [[x,y],...]
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->unique(['project_id', 'slug']);
        });

        // ── Этажи (floors) ──
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('number');
            $table->string('plan_image')->nullable();      // архитектурный план этажа
            $table->json('facade_polygon')->nullable();    // полоса этажа на фасаде корпуса
            $table->timestamps();
            $table->unique(['block_id', 'number']);
        });

        // ── Квартиры: привязка к корпусу/этажу-плану + характеристики ──
        Schema::table('apartments', function (Blueprint $table) {
            $table->foreignId('block_id')->nullable()->after('project_id')
                ->constrained('blocks')->nullOnDelete();
            $table->json('polygon')->nullable()->after('plan_image');   // контур на плане этажа
            $table->decimal('ceiling_height', 4, 2)->nullable()->after('area'); // м
            $table->unsignedTinyInteger('bathrooms')->nullable()->after('ceiling_height');
            $table->boolean('balcony')->default(false)->after('bathrooms');
            $table->string('view_type', 100)->nullable()->after('balcony');    // вид из окна
        });

        // Статус «Скоро» (в продаже позже) — расширяем enum
        DB::statement("ALTER TABLE apartments MODIFY status ENUM('available','reserved','sold','soon') NOT NULL DEFAULT 'available'");

        // ── Проект: генплан ──
        Schema::table('projects', function (Blueprint $table) {
            $table->string('masterplan_image')->nullable()->after('cover_image');
            // Декоративные зоны генплана: [{type,label,polygon},...]
            // type: road|parking|playground|pool|leisure|commercial|green
            $table->json('masterplan_pois')->nullable()->after('masterplan_image');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['masterplan_image', 'masterplan_pois']);
        });
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('block_id');
            $table->dropColumn(['polygon', 'ceiling_height', 'bathrooms', 'balcony', 'view_type']);
        });
        DB::statement("ALTER TABLE apartments MODIFY status ENUM('available','reserved','sold') NOT NULL DEFAULT 'available'");
        Schema::dropIfExists('floors');
        Schema::dropIfExists('blocks');
    }
};
