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
        Schema::table('leads', function (Blueprint $table) {
            // CRM enrichment fields
            $table->unsignedTinyInteger('score')->default(0)->after('notes');        // 0-100
            $table->enum('temperature', ['cold','warm','hot'])->default('cold')->after('score');
            $table->string('utm_source')->nullable()->after('temperature');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
            $table->string('referrer_url')->nullable()->after('utm_campaign');
            $table->string('landing_page')->nullable()->after('referrer_url');
            $table->string('budget_range', 50)->nullable()->after('landing_page');
            $table->string('pipeline_stage', 30)->default('new')->after('budget_range');
            // new|contacted|qualified|proposal|converted|lost
            $table->timestamp('last_activity_at')->nullable()->after('pipeline_stage');
            $table->timestamp('next_follow_up_at')->nullable()->after('last_activity_at');
            $table->json('tags')->nullable()->after('next_follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'score','temperature','utm_source','utm_medium','utm_campaign',
                'referrer_url','landing_page','budget_range','pipeline_stage',
                'last_activity_at','next_follow_up_at','tags',
            ]);
        });
    }
};
