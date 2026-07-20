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
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            // page key: home|objects|mining|about|services|investors|contacts|progress
            $table->string('page', 60)->index();
            // section key: hero|stats|cards|cta|partners|testimonials|faq...
            $table->string('section', 80);
            $table->string('locale', 5)->default('ru'); // ru|en|tj
            // all content as flexible JSON
            $table->json('content')->nullable();
            // per-block settings (bg color, visible, order...)
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('position')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['page', 'section', 'locale']);
            $table->index(['page', 'locale', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
