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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country');
            $table->enum('type', ['buyer', 'supplier', 'investor', 'contractor', 'government'])->default('buyer');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->enum('contract_status', ['active', 'negotiation', 'expired', 'terminated'])->default('negotiation');
            $table->date('partnership_since')->nullable();
            $table->decimal('annual_volume', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
