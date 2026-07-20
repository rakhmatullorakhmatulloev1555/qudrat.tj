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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('passport')->nullable();
            $table->enum('type', ['individual', 'company'])->default('individual');
            $table->enum('status', ['lead', 'active', 'vip', 'inactive'])->default('active');
            $table->enum('source', ['website', 'phone', 'referral', 'social', 'other'])->default('website');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
