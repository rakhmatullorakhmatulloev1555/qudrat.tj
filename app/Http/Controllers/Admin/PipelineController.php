<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PipelineController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────
    public function index()
    {
        $pipelines = Pipeline::with(['stages' => fn($q) => $q->orderBy('position')])
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'type'       => $p->type,
                'is_default' => $p->is_default,
                'is_active'  => $p->is_active,
                'stages'     => $p->stages->map(fn($s) => [
                    'id'          => $s->id,
                    'name'        => $s->name,
                    'key'         => $s->key,
                    'color'       => $s->color,
                    'probability' => $s->probability,
                    'position'    => $s->position,
                    'is_won'      => $s->is_won,
                    'is_lost'     => $s->is_lost,
                    'leads_count' => $s->leads()->count(),
                    'deals_count' => $s->deals()->count(),
                ]),
            ]);

        return Inertia::render('Admin/Pipeline/Index', [
            'pipelines' => $pipelines,
        ]);
    }

    // ── Store Pipeline ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        Gate::authorize('manage');
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'type' => 'required|in:sales,real_estate,mining,custom',
        ]);

        Pipeline::create([
            'name'       => $data['name'],
            'type'       => $data['type'],
            'is_default' => false,
            'is_active'  => true,
        ]);

        return back()->with('success', 'Воронка создана.');
    }

    // ── Update Pipeline ───────────────────────────────────────────────────
    public function update(Request $request, Pipeline $pipeline)
    {
        Gate::authorize('manage');
        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'type'      => 'required|in:sales,real_estate,mining,custom',
            'is_active' => 'boolean',
        ]);

        $pipeline->update($data);

        return back()->with('success', 'Воронка обновлена.');
    }

    // ── Set Default ───────────────────────────────────────────────────────
    public function setDefault(Pipeline $pipeline)
    {
        Gate::authorize('manage');
        Pipeline::query()->update(['is_default' => false]);
        $pipeline->update(['is_default' => true]);

        return back()->with('success', "«{$pipeline->name}» — воронка по умолчанию.");
    }

    // ── Destroy Pipeline ──────────────────────────────────────────────────
    public function destroy(Pipeline $pipeline)
    {
        Gate::authorize('delete-record');
        if ($pipeline->is_default) {
            return back()->with('error', 'Нельзя удалить воронку по умолчанию.');
        }
        if ($pipeline->stages()->exists()) {
            $pipeline->stages()->delete();
        }
        $pipeline->delete();

        return back()->with('success', 'Воронка удалена.');
    }

    // ── Store Stage ───────────────────────────────────────────────────────
    public function storeStage(Request $request, Pipeline $pipeline)
    {
        Gate::authorize('manage');
        $data = $request->validate([
            'name'        => 'required|string|max:80',
            'color'       => 'required|string|max:20',
            'probability' => 'required|integer|min:0|max:100',
            'is_won'      => 'boolean',
            'is_lost'     => 'boolean',
        ]);

        // Auto-generate unique key from name
        $baseKey = Str::slug($data['name'], '_');
        $key = $baseKey;
        $i   = 2;
        while (PipelineStage::where('pipeline_id', $pipeline->id)->where('key', $key)->exists()) {
            $key = "{$baseKey}_{$i}";
            $i++;
        }

        $maxPos = $pipeline->stages()->max('position') ?? -1;

        // Если is_won или is_lost — снимаем флаг у остальных (один победитель / один проигравший)
        if (!empty($data['is_won']))  $pipeline->stages()->update(['is_won'  => false]);
        if (!empty($data['is_lost'])) $pipeline->stages()->update(['is_lost' => false]);

        $pipeline->stages()->create([
            'name'        => $data['name'],
            'key'         => $key,
            'color'       => $data['color'],
            'probability' => $data['probability'],
            'position'    => $maxPos + 1,
            'is_won'      => $data['is_won'] ?? false,
            'is_lost'     => $data['is_lost'] ?? false,
        ]);

        return back()->with('success', 'Стадия добавлена.');
    }

    // ── Update Stage ──────────────────────────────────────────────────────
    public function updateStage(Request $request, Pipeline $pipeline, PipelineStage $stage)
    {
        Gate::authorize('manage');
        abort_unless($stage->pipeline_id === $pipeline->id, 404);

        $data = $request->validate([
            'name'        => 'required|string|max:80',
            'color'       => 'required|string|max:20',
            'probability' => 'required|integer|min:0|max:100',
            'is_won'      => 'boolean',
            'is_lost'     => 'boolean',
        ]);

        if (!empty($data['is_won']))  $pipeline->stages()->where('id', '!=', $stage->id)->update(['is_won'  => false]);
        if (!empty($data['is_lost'])) $pipeline->stages()->where('id', '!=', $stage->id)->update(['is_lost' => false]);

        $stage->update($data);

        return back()->with('success', 'Стадия обновлена.');
    }

    // ── Reorder Stages ────────────────────────────────────────────────────
    public function reorderStages(Request $request, Pipeline $pipeline)
    {
        Gate::authorize('manage');
        $data = $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:pipeline_stages,id',
        ]);

        foreach ($data['order'] as $position => $stageId) {
            PipelineStage::where('id', $stageId)
                ->where('pipeline_id', $pipeline->id)
                ->update(['position' => $position]);
        }

        return response()->json(['ok' => true]);
    }

    // ── Destroy Stage ─────────────────────────────────────────────────────
    public function destroyStage(Pipeline $pipeline, PipelineStage $stage)
    {
        Gate::authorize('delete-record');
        abort_unless($stage->pipeline_id === $pipeline->id, 404);

        if ($stage->leads()->count() > 0 || $stage->deals()->count() > 0) {
            return back()->with('error', 'Нельзя удалить стадию, к которой привязаны лиды или сделки.');
        }

        $stage->delete();

        // Пересчитываем позиции
        $pipeline->stages()->orderBy('position')->get()
            ->each(fn($s, $i) => $s->update(['position' => $i]));

        return back()->with('success', 'Стадия удалена.');
    }
}
