<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('title');
            $table->foreignId('pipeline_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stage_id')->nullable()->constrained('pipeline_stages')->nullOnDelete();
            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->unsignedBigInteger('contact_id')->nullable()->index(); // Client
            $table->unsignedBigInteger('apartment_id')->nullable()->index();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->unsignedTinyInteger('probability')->default(0);
            $table->date('expected_close_date')->nullable();
            $table->timestamp('closed_at')->nullable();
            // open|won|lost|frozen
            $table->string('status', 20)->default('open');
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
