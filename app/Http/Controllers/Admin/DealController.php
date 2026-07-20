<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DealController extends Controller
{
    // ─── Kanban ───────────────────────────────────────────────────────────
    public function kanban(Request $request)
    {
        $pipeline = $request->filled('pipeline_id')
            ? Pipeline::with('stages')->findOrFail($request->pipeline_id)
            : Pipeline::with('stages')->where('is_default', true)->first()
              ?? Pipeline::with('stages')->first();

        $dealsQuery = Deal::with(['lead:id,name,phone', 'assignee:id,name', 'stage:id,name,color,key'])
            ->where('pipeline_id', $pipeline->id)
            ->orderByDesc('amount');

        if ($request->filled('status')) $dealsQuery->where('status', $request->status);
        if ($request->filled('search')) {
            $dealsQuery->where('title', 'like', "%{$request->search}%");
        }

        $allDeals = $dealsQuery->get();

        $columns = $pipeline->stages->map(function ($stage) use ($allDeals) {
            $stageDeals = $allDeals->where('stage_id', $stage->id)->values();
            return [
                'id'           => $stage->id,
                'key'          => $stage->key,
                'name'         => $stage->name,
                'color'        => $stage->color,
                'probability'  => $stage->probability,
                'is_won'       => $stage->is_won,
                'is_lost'      => $stage->is_lost,
                'total_amount' => (float) $stageDeals->sum('amount'),
                'deals'        => $stageDeals->map(fn($d) => [
                    'id'                  => $d->id,
                    'title'               => $d->title,
                    'amount'              => (float) $d->amount,
                    'currency'            => $d->currency ?? 'USD',
                    'probability'         => $d->probability,
                    'status'              => $d->status,
                    'expected_close_date' => $d->expected_close_date?->format('d.m.Y'),
                    'lead'                => $d->lead?->only('id', 'name', 'phone'),
                    'assignee'            => $d->assignee?->only('id', 'name'),
                    'stage'               => $d->stage?->only('id', 'name', 'color', 'key'),
                ]),
            ];
        });

        return Inertia::render('Admin/Deals/Kanban', [
            'pipeline'  => ['id' => $pipeline->id, 'name' => $pipeline->name],
            'pipelines' => Pipeline::select('id', 'name', 'is_default')->get(),
            'columns'   => $columns,
            'managers'  => User::whereIn('role', ['admin', 'manager'])->select('id', 'name')->get(),
            'leads'     => Lead::select('id', 'name', 'phone')->latest()->limit(100)->get(),
            'filters'   => $request->only(['pipeline_id', 'status', 'search']),
            'stats'     => [
                'total_deals' => $allDeals->count(),
                'open_value'  => (float) $allDeals->where('status', 'open')->sum('amount'),
                'won_value'   => (float) Deal::where('pipeline_id', $pipeline->id)->where('status', 'won')->sum('amount'),
                'won_count'   => Deal::where('pipeline_id', $pipeline->id)->where('status', 'won')->count(),
            ],
        ]);
    }

    // ─── List ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Deal::with(['pipeline', 'stage', 'lead', 'assignee'])->latest();

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('pipeline_id')) $query->where('pipeline_id', $request->pipeline_id);
        if ($request->filled('assigned_to')) $query->where('assigned_to', $request->assigned_to);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%");
            });
        }

        $pipelines = Pipeline::with('stages')->get();

        return Inertia::render('Admin/Deals/Index', [
            'deals'     => $query->paginate(20)->withQueryString(),
            'pipelines' => $pipelines,
            'managers'  => User::whereIn('role', ['admin', 'manager'])->select('id', 'name')->get(),
            'filters'   => $request->only(['status', 'pipeline_id', 'assigned_to', 'search']),
            'stats'     => [
                'total'    => Deal::count(),
                'open'     => Deal::where('status', 'open')->count(),
                'won'      => Deal::where('status', 'won')->count(),
                'lost'     => Deal::where('status', 'lost')->count(),
                'total_value' => Deal::where('status', 'open')->sum('amount'),
            ],
        ]);
    }

    // ─── Store ────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'pipeline_id'         => 'nullable|exists:pipelines,id',
            'stage_id'            => 'nullable|exists:pipeline_stages,id',
            'lead_id'             => 'nullable|exists:leads,id',
            'contact_id'          => 'nullable|exists:clients,id',
            'apartment_id'        => 'nullable|exists:apartments,id',
            'amount'              => 'nullable|numeric|min:0',
            'currency'            => 'nullable|string|max:10',
            'probability'         => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'status'              => 'nullable|in:open,won,lost,frozen',
            'assigned_to'         => 'nullable|exists:users,id',
            'notes'               => 'nullable|string',
            'tags'                => 'nullable|array',
        ]);

        $data['created_by'] = auth()->id();
        $deal = Deal::create($data);

        AuditLog::record('created', 'Deals', "Сделка #{$deal->id} «{$deal->title}» создана", [], $deal);

        return back()->with('success', 'Сделка создана.');
    }

    // ─── Update ───────────────────────────────────────────────────────────
    public function update(Request $request, Deal $deal)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'pipeline_id'         => 'nullable|exists:pipelines,id',
            'stage_id'            => 'nullable|exists:pipeline_stages,id',
            'lead_id'             => 'nullable|exists:leads,id',
            'contact_id'          => 'nullable|exists:clients,id',
            'apartment_id'        => 'nullable|exists:apartments,id',
            'amount'              => 'nullable|numeric|min:0',
            'currency'            => 'nullable|string|max:10',
            'probability'         => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'status'              => 'nullable|in:open,won,lost,frozen',
            'assigned_to'         => 'nullable|exists:users,id',
            'notes'               => 'nullable|string',
            'tags'                => 'nullable|array',
        ]);

        // Auto-set closed_at on status change to won/lost
        if (isset($data['status']) && in_array($data['status'], ['won', 'lost']) && !$deal->closed_at) {
            $data['closed_at'] = now();
        }
        if (isset($data['status']) && $data['status'] === 'open') {
            $data['closed_at'] = null;
        }

        $old = $deal->only(array_keys($data));
        $deal->update($data);

        AuditLog::record('updated', 'Deals', "Сделка #{$deal->id} обновлена", [], $deal, $old, $data);

        return back()->with('success', 'Сделка обновлена.');
    }

    // ─── Move stage (Kanban / quick update) ──────────────────────────────
    public function moveStage(Request $request, Deal $deal)
    {
        $data = $request->validate([
            'stage_id' => 'required|exists:pipeline_stages,id',
        ]);

        $stage = \App\Models\PipelineStage::find($data['stage_id']);
        $oldStageId = $deal->stage_id;
        $updates = ['stage_id' => $data['stage_id']];

        if ($stage->is_won)  { $updates['status'] = 'won';  $updates['closed_at'] = now(); }
        if ($stage->is_lost) { $updates['status'] = 'lost'; $updates['closed_at'] = now(); }

        $deal->update($updates);

        // Log activity
        $deal->activities()->create([
            'type'        => 'status_change',
            'subject'     => 'Этап изменён',
            'body'        => "Сделка перемещена в «{$stage->name}»",
            'user_id'     => auth()->id(),
            'direction'   => 'internal',
            'is_done'     => true,
            'completed_at'=> now(),
        ]);

        return response()->json(['ok' => true, 'deal' => $deal->fresh()->load('stage')]);
    }

    // ─── Destroy ─────────────────────────────────────────────────────────
    public function destroy(Deal $deal)
    {
        Gate::authorize('delete-record');
        AuditLog::record('deleted', 'Deals', "Сделка #{$deal->id} «{$deal->title}» удалена");
        $deal->delete();
        return back()->with('success', 'Сделка удалена.');
    }
}
