<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Расширяем ENUM — добавляем роль 'client'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','viewer','client') NOT NULL DEFAULT 'viewer'");

        Schema::table('users', function (Blueprint $table) {
            $table->string('interest', 50)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('interest');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','viewer') NOT NULL DEFAULT 'viewer'");
    }
};
