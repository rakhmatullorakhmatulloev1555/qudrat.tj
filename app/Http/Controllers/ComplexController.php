<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Интерактивный выбор квартиры:
 * Генплан (/complex/{project}) → Корпус ({block}) → Этаж (floor-{n}) → Карточка квартиры.
 */
class ComplexController extends Controller
{
    /** Уровень 1 — генеральный план комплекса. */
    public function master(Project $project): Response
    {
        abort_unless($project->is_published, 404);

        $blocks = $project->blocks()->published()->ordered()->get()
            ->map(function (Block $b) {
                $summary = $b->salesSummary();
                return [
                    'id'           => $b->id,
                    'slug'         => $b->slug,
                    'name'         => $b->name,
                    'floors'       => $b->floors_total,
                    'apartments'   => $summary['apartments'],
                    'sold_percent' => $summary['sold_percent'],
                    'available'    => $b->apartments()->where('status', 'available')->count(),
                    'polygon'      => $b->masterplan_polygon,
                    'facade'       => $b->facade_image,
                ];
            });

        return Inertia::render('Complex/Master', [
            'project' => [
                'name'       => $project->name,
                'slug'       => $project->slug,
                'city'       => $project->city,
                'address'    => $project->address,
                'masterplan' => $project->masterplan_image,
                'pois'       => $project->masterplan_pois ?? [],
            ],
            'blocks' => $blocks,
            'stats'  => [
                'blocks'     => $blocks->count(),
                'apartments' => $project->apartments()->count(),
                'available'  => $project->apartments()->where('status', 'available')->count(),
            ],
        ]);
    }

    /** Уровень 2 — корпус: фасад с кликабельными этажами. */
    public function block(Project $project, string $blockSlug): Response
    {
        abort_unless($project->is_published, 404);

        $block = $project->blocks()->published()->where('slug', $blockSlug)->firstOrFail();

        // Доступность по этажам одним запросом
        $byFloor = Apartment::where('block_id', $block->id)
            ->selectRaw("floor,
                COUNT(*) as total,
                SUM(status = 'available') as available")
            ->groupBy('floor')
            ->get()
            ->keyBy('floor');

        $floorRows = $block->floors()->get()->keyBy('number');

        $floors = collect(range($block->floors_total, 1))->map(fn($n) => [
            'number'    => $n,
            'total'     => (int) ($byFloor[$n]->total ?? 0),
            'available' => (int) ($byFloor[$n]->available ?? 0),
            'polygon'   => $floorRows[$n]->facade_polygon ?? null,
            'has_plan'  => (bool) ($floorRows[$n]->plan_image ?? false),
        ])->values();

        return Inertia::render('Complex/Block', [
            'project' => ['name' => $project->name, 'slug' => $project->slug],
            'block'   => [
                'name'        => $block->name,
                'slug'        => $block->slug,
                'floorsTotal' => $block->floors_total,
                'facade'      => $block->facade_image,
                'description' => $block->description,
            ],
            'floors' => $floors,
        ]);
    }

    /** Уровень 3 — план этажа с квартирами. */
    public function floor(Project $project, string $blockSlug, int $number): Response
    {
        abort_unless($project->is_published, 404);

        $block = $project->blocks()->published()->where('slug', $blockSlug)->firstOrFail();
        abort_if($number < 1 || $number > $block->floors_total, 404);

        $floor = $block->floors()->where('number', $number)->first();

        $apartments = Apartment::where('block_id', $block->id)
            ->where('floor', $number)
            ->orderBy('number')
            ->get(['id', 'number', 'rooms', 'area', 'price', 'currency', 'status', 'finish', 'polygon', 'plan_image', 'balcony', 'bathrooms'])
            ->map(fn($a) => [
                'id'       => $a->id,
                'number'   => $a->number,
                'rooms'    => $a->rooms,
                'area'     => (float) $a->area,
                'price'    => (float) $a->price,
                'currency' => $a->currency,
                'status'   => $a->status,
                'polygon'  => $a->polygon,
                'balcony'  => $a->balcony,
            ]);

        return Inertia::render('Complex/Floor', [
            'project'    => ['name' => $project->name, 'slug' => $project->slug],
            'block'      => ['name' => $block->name, 'slug' => $block->slug, 'floorsTotal' => $block->floors_total],
            'floor'      => [
                'number' => $number,
                'plan'   => $floor?->plan_image,
            ],
            'apartments' => $apartments,
        ]);
    }
}
