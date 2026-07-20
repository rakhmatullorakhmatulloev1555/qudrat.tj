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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['contract', 'invoice', 'certificate', 'permit', 'act', 'report', 'power_of_attorney', 'other'])->default('contract');
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable()->index();
            $table->string('related_name')->nullable();
            $table->enum('status', ['draft', 'review', 'signed', 'active', 'expired', 'archived'])->default('draft');
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->integer('file_size')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('signed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
