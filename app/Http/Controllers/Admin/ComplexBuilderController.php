<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\AuditLog;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

/**
 * Конструктор интерактивного комплекса:
 * генплан проекта → корпуса (полигоны) → этажи (планы) → квартиры (контуры).
 */
class ComplexBuilderController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::orderBy('name')->get(['id', 'name', 'slug', 'is_published']);
        $projectId = (int) ($request->query('project') ?: $projects->first()?->id);
        $project = $projects->firstWhere('id', $projectId)
            ? Project::find($projectId)
            : null;

        $blocks = $project
            ? $project->blocks()->ordered()->with('floors')->get()->map(fn(Block $b) => [
                'id'                 => $b->id,
                'name'               => $b->name,
                'slug'               => $b->slug,
                'floors_total'       => $b->floors_total,
                'facade_image'       => $b->facade_image,
                'masterplan_polygon' => $b->masterplan_polygon,
                'description'        => $b->description,
                'sort_order'         => $b->sort_order,
                'is_published'       => $b->is_published,
                'apartments_count'   => $b->apartments()->count(),
                'floors'             => $b->floors->map(fn(Floor $f) => [
                    'id'             => $f->id,
                    'number'         => $f->number,
                    'plan_image'     => $f->plan_image,
                    'facade_polygon' => $f->facade_polygon,
                ])->values(),
            ])
            : collect();

        // Квартиры выбранного проекта — для привязки к корпусам/контурам
        $apartments = $project
            ? Apartment::where('project_id', $project->id)
                ->orderBy('floor')->orderBy('number')
                ->get(['id', 'number', 'floor', 'rooms', 'area', 'status', 'block_id', 'polygon'])
            : collect();

        return Inertia::render('Admin/Complex/Index', [
            'projects'   => $projects,
            'project'    => $project ? [
                'id'               => $project->id,
                'name'             => $project->name,
                'slug'             => $project->slug,
                'masterplan_image' => $project->masterplan_image,
                'masterplan_pois'  => $project->masterplan_pois ?? [],
            ] : null,
            'blocks'     => $blocks,
            'apartments' => $apartments,
        ]);
    }

    /** Генплан: изображение + декоративные зоны (POI). */
    public function saveMasterplan(Request $request, Project $project)
    {
        Gate::authorize('manage');

        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'pois'  => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteStorageFile($project->masterplan_image);
            $project->masterplan_image = '/storage/' . $request->file('image')->store('complex', 'public');
        }

        if ($request->has('pois')) {
            $project->masterplan_pois = collect($request->input('pois', []))
                ->filter(fn($p) => !empty($p['polygon']) && count($p['polygon']) >= 3)
                ->map(fn($p) => [
                    'type'    => in_array($p['type'] ?? '', ['road', 'parking', 'playground', 'pool', 'leisure', 'commercial', 'green']) ? $p['type'] : 'green',
                    'label'   => (string) ($p['label'] ?? ''),
                    'polygon' => $p['polygon'],
                ])->values()->all();
        }

        $project->save();
        AuditLog::record('updated', 'Complex', "Генплан проекта «{$project->name}» обновлён", [], $project);

        return back()->with('success', 'Генплан сохранён.');
    }

    // ── Корпуса ──

    public function storeBlock(Request $request, Project $project)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'floors_total' => 'required|integer|min:1|max:200',
            'description'  => 'nullable|string|max:2000',
        ]);

        $block = $project->blocks()->create([
            ...$data,
            'slug'       => Block::makeSlug($project->id, $data['name']),
            'sort_order' => ($project->blocks()->max('sort_order') ?? 0) + 1,
        ]);

        AuditLog::record('created', 'Complex', "Корпус «{$block->name}» создан в «{$project->name}»", [], $block);
        return back()->with('success', 'Корпус создан.');
    }

    public function updateBlock(Request $request, Block $block)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'               => 'sometimes|required|string|max:100',
            'floors_total'       => 'sometimes|required|integer|min:1|max:200',
            'description'        => 'nullable|string|max:2000',
            'is_published'       => 'sometimes|boolean',
            'masterplan_polygon' => 'sometimes|nullable|array',
            'facade'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        if ($request->hasFile('facade')) {
            $this->deleteStorageFile($block->facade_image);
            $data['facade_image'] = '/storage/' . $request->file('facade')->store('complex', 'public');
        }
        unset($data['facade']);

        $old = $block->only(array_keys($data));
        $block->update($data);

        AuditLog::record('updated', 'Complex', "Корпус «{$block->name}» обновлён", [], $block, $old, $data);
        return back()->with('success', 'Корпус обновлён.');
    }

    public function destroyBlock(Block $block)
    {
        Gate::authorize('delete-record');

        $this->deleteStorageFile($block->facade_image);
        foreach ($block->floors as $floor) {
            $this->deleteStorageFile($floor->plan_image);
        }
        // Отвязываем квартиры (block_id → null через nullOnDelete)
        $name = $block->name;
        $block->delete();

        AuditLog::record('deleted', 'Complex', "Корпус «{$name}» удалён");
        return back()->with('success', 'Корпус удалён.');
    }

    public function reorderBlocks(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        foreach ($data['ids'] as $i => $id) {
            Block::where('id', $id)->update(['sort_order' => $i]);
        }
        return back();
    }

    // ── Этажи ──

    /** Создаёт недостающие строки этажей 1..floors_total. */
    public function generateFloors(Block $block)
    {
        Gate::authorize('manage');

        $existing = $block->floors()->pluck('number')->all();
        $created = 0;
        foreach (range(1, $block->floors_total) as $n) {
            if (!in_array($n, $existing)) {
                $block->floors()->create(['number' => $n]);
                $created++;
            }
        }

        return back()->with('success', "Создано этажей: {$created}.");
    }

    public function updateFloor(Request $request, Floor $floor)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'facade_polygon' => 'sometimes|nullable|array',
            'plan'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        if ($request->hasFile('plan')) {
            $this->deleteStorageFile($floor->plan_image);
            $data['plan_image'] = '/storage/' . $request->file('plan')->store('complex', 'public');
        }
        unset($data['plan']);

        $floor->update($data);

        AuditLog::record('updated', 'Complex', "Этаж {$floor->number} корпуса «{$floor->block->name}» обновлён", [], $floor);
        return back()->with('success', 'Этаж обновлён.');
    }

    // ── Контуры квартир ──

    /** Привязка квартиры к корпусу + контур на плане этажа. */
    public function saveApartmentShape(Request $request, Apartment $apartment)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'block_id' => 'nullable|integer|exists:blocks,id',
            'polygon'  => 'nullable|array',
        ]);

        $old = $apartment->only(['block_id', 'polygon']);
        $apartment->update($data);

        AuditLog::record('updated', 'Complex', "Контур квартиры №{$apartment->number} обновлён", [], $apartment, $old, $data);
        return back()->with('success', "Квартира №{$apartment->number} сохранена.");
    }

    private function deleteStorageFile(?string $path): void
    {
        if ($path && str_starts_with($path, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $path));
        }
    }
}
