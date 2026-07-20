<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pipelines
        Schema::create('pipelines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 30)->default('sales'); // sales|real_estate|custom
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pipeline stages
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('key', 30); // new|contacted|qualified|proposal|converted|lost
            $table->string('color', 20)->default('#6366F1');
            $table->unsignedTinyInteger('probability')->default(0); // 0-100 %
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);
            $table->json('auto_actions')->nullable();
            $table->timestamps();
        });

        // Seed default pipeline
        $pipelineId = DB::table('pipelines')->insertGetId([
            'name'       => 'Продажи недвижимости',
            'type'       => 'real_estate',
            'is_default' => true,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stages = [
            ['key'=>'new',       'name'=>'Новый',        'color'=>'#6366F1', 'probability'=>5,   'position'=>0],
            ['key'=>'contacted', 'name'=>'Контакт',      'color'=>'#3B82F6', 'probability'=>20,  'position'=>1],
            ['key'=>'qualified', 'name'=>'Квалифицирован','color'=>'#F59E0B','probability'=>40,  'position'=>2],
            ['key'=>'proposal',  'name'=>'Предложение',  'color'=>'#8B5CF6', 'probability'=>65,  'position'=>3],
            ['key'=>'converted', 'name'=>'Продажа',      'color'=>'#10B981', 'probability'=>100, 'position'=>4, 'is_won'=>true],
            ['key'=>'lost',      'name'=>'Отказ',        'color'=>'#EF4444', 'probability'=>0,   'position'=>5, 'is_lost'=>true],
        ];

        foreach ($stages as $s) {
            DB::table('pipeline_stages')->insert(array_merge($s, [
                'pipeline_id' => $pipelineId,
                'is_won'      => $s['is_won'] ?? false,
                'is_lost'     => $s['is_lost'] ?? false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
        Schema::dropIfExists('pipelines');
    }
};
