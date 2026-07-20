<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Демо-данные интерактивного комплекса для проекта Qudrat City:
 * генплан + Block A (9 эт.) / Block B (7 эт.), этажи, контуры квартир.
 * Полигоны соответствуют геометрии demo-изображений (см. gen_complex_demo.php).
 * Повторный запуск безопасен.
 */
class ComplexDemoSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('name', 'Qudrat City')->orWhere('slug', 'like', 'qudrat-city%')->first();
        if (!$project) {
            $this->command?->warn('Проект Qudrat City не найден — демо не посеяно.');
            return;
        }

        // ── Генплан ──
        $project->update([
            'masterplan_image' => '/images/complex/demo-masterplan.webp',
            'masterplan_pois'  => [
                ['type' => 'pool',       'label' => 'Бассейн',          'polygon' => [[388, 150], [475, 150], [475, 217], [388, 217]]],
                ['type' => 'playground', 'label' => 'Детская площадка', 'polygon' => [[388, 433], [488, 433], [488, 533], [388, 533]]],
                ['type' => 'green',      'label' => 'Парк',             'polygon' => [[844, 125], [969, 125], [969, 583], [844, 583]]],
                ['type' => 'parking',    'label' => 'Парковка',         'polygon' => [[150, 900], [400, 900], [400, 983], [150, 983]]],
                ['type' => 'road',       'label' => 'Проезд',           'polygon' => [[0, 792], [1000, 792], [1000, 875], [0, 875]]],
            ],
        ]);

        // ── Block A (9 этажей, с фасадом и планами) ──
        $blockA = Block::updateOrCreate(
            ['project_id' => $project->id, 'slug' => 'block-a'],
            [
                'name'               => 'Блок A',
                'floors_total'       => 9,
                'facade_image'       => '/images/complex/demo-facade.webp',
                'masterplan_polygon' => [[138, 233], [350, 233], [350, 633], [138, 633]],
                'description'        => 'Первая очередь строительства — 9 этажей, подземный паркинг, панорамные виды на парк.',
                'sort_order'         => 0,
            ]
        );

        // ── Block B (7 этажей, без фасада — покажет фолбэк-список) ──
        Block::updateOrCreate(
            ['project_id' => $project->id, 'slug' => 'block-b'],
            [
                'name'               => 'Блок B',
                'floors_total'       => 7,
                'masterplan_polygon' => [[594, 267], [806, 267], [806, 667], [594, 667]],
                'description'        => 'Вторая очередь — старт продаж скоро.',
                'sort_order'         => 1,
            ]
        );

        // ── Этажи Block A: полоса на фасаде + план ──
        // Фасад 1200x1500: этаж n — y от 100+(9-n)*93.33, высота 93.33 (в коорд. 0-1000)
        foreach (range(1, 9) as $n) {
            $yTop = (int) round(100 + (9 - $n) * 93.33);
            $yBot = (int) round(100 + (10 - $n) * 93.33);
            $blockA->floors()->updateOrCreate(
                ['number' => $n],
                [
                    'plan_image'     => '/images/complex/demo-floorplan.webp',
                    'facade_polygon' => [[167, $yTop], [833, $yTop], [833, $yBot], [167, $yBot]],
                ]
            );
        }

        // ── Контуры квартир: юниты на плане (0-1000, из geometry gen-скрипта) ──
        $units = [
            [[63, 83],  [344, 83],  [344, 458], [63, 458]],   // APT 1
            [[344, 83], [625, 83],  [625, 458], [344, 458]],  // APT 2
            [[625, 83], [938, 83],  [938, 458], [625, 458]],  // APT 3
            [[63, 542], [500, 542], [500, 917], [63, 917]],   // APT 4
            [[500, 542],[938, 542], [938, 917], [500, 917]],  // APT 5
        ];

        $byFloor = Apartment::where('project_id', $project->id)
            ->orderBy('number')
            ->get()
            ->groupBy('floor');

        $assigned = 0;
        foreach ($byFloor as $apartments) {
            foreach ($apartments->values() as $i => $apt) {
                $apt->update([
                    'block_id' => $blockA->id,
                    'polygon'  => $units[$i] ?? null, // больше 5 на этаже — без контура (останутся в списке)
                ]);
                $assigned++;
            }
        }

        $this->command?->info("Demo complex: Block A/B, 9 этажей, квартир привязано: {$assigned}.");
    }
}
