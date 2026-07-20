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

class LeadController extends Controller
{
    // ─── List (table view) ────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Lead::with('assignee')->latest();

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('temperature')) $query->where('temperature', $request->temperature);
        if ($request->filled('assigned_to')) $query->where('assigned_to', $request->assigned_to);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name',  'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        return Inertia::render('Admin/Leads/Index', [
            'leads'    => $query->paginate(20)->withQueryString(),
            'managers' => User::whereIn('role', ['admin', 'manager'])->select('id', 'name')->get(),
            'filters'  => $request->only(['status', 'temperature', 'assigned_to', 'search']),
            'stats'    => [
                'total'    => Lead::count(),
                'new'      => Lead::where('status', 'new')->count(),
                'hot'      => Lead::where('temperature', 'hot')->count(),
                'won'      => Lead::where('status', 'success')->count(),
            ],
        ]);
    }

    // ─── Kanban board ─────────────────────────────────────────────────────
    public function kanban(Request $request)
    {
        $pipeline = Pipeline::where('is_default', true)->with('stages')->first()
                 ?? Pipeline::with('stages')->first();

        if (!$pipeline) {
            return Inertia::render('Admin/Leads/Kanban', [
                'pipeline' => null,
                'columns'  => [],
                'managers' => [],
            ]);
        }

        $query = Lead::with(['assignee'])->orderByDesc('updated_at');

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('temperature')) {
            $query->where('temperature', $request->temperature);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name',  'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $leads = $query->get();

        $columns = $pipeline->stages->map(function ($stage) use ($leads) {
            return [
                'id'          => $stage->id,
                'key'         => $stage->key,
                'name'        => $stage->name,
                'color'       => $stage->color,
                'probability' => $stage->probability,
                'is_won'      => $stage->is_won,
                'is_lost'     => $stage->is_lost,
                'leads'       => $leads->where('pipeline_stage', $stage->key)
                                       ->values()
                                       ->map(fn($l) => $this->leadCard($l)),
            ];
        });

        // Unmapped leads → first stage
        $allStageKeys = $pipeline->stages->pluck('key')->toArray();
        $unmapped = $leads->filter(fn($l) => !in_array($l->pipeline_stage, $allStageKeys));
        if ($unmapped->isNotEmpty() && $columns->isNotEmpty()) {
            $columns[0]['leads'] = collect($columns[0]['leads'])
                ->concat($unmapped->map(fn($l) => $this->leadCard($l)))
                ->values()
                ->toArray();
        }

        return Inertia::render('Admin/Leads/Kanban', [
            'pipeline' => $pipeline->only('id', 'name', 'type'),
            'columns'  => $columns,
            'managers' => User::whereIn('role', ['admin', 'manager'])->select('id', 'name')->get(),
            'filters'  => $request->only(['assigned_to', 'temperature', 'search']),
        ]);
    }

    private function leadCard(Lead $lead): array
    {
        return [
            'id'          => $lead->id,
            'name'        => $lead->name,
            'phone'       => $lead->phone,
            'email'       => $lead->email,
            'temperature' => $lead->temperature ?? 'cold',
            'score'       => $lead->score ?? 0,
            'source'      => $lead->source,
            'interest'    => $lead->interest,
            'budget_range'=> $lead->budget_range,
            'assignee'    => $lead->assignee?->only('id', 'name'),
            'created_at'  => $lead->created_at,
            'updated_at'  => $lead->updated_at,
            'next_follow_up_at' => $lead->next_follow_up_at,
        ];
    }

    // ─── 360° profile ─────────────────────────────────────────────────────
    public function show(Lead $lead)
    {
        $lead->load([
            'assignee',
            'client',
            'activities.user',
            'deals.stage',
            'deals.pipeline',
        ]);

        $pipeline = Pipeline::where('is_default', true)->with('stages')->first();

        return Inertia::render('Admin/Leads/Show', [
            'lead'      => $lead,
            'pipeline'  => $pipeline?->only('id', 'name'),
            'stages'    => $pipeline?->stages->map(fn($s) => $s->only('id','key','name','color','probability','is_won','is_lost')) ?? [],
            'managers'  => User::whereIn('role', ['admin', 'manager'])->select('id', 'name')->get(),
            'activityTypes' => [
                'call','email','note','task','meeting','whatsapp','telegram',
            ],
        ]);
    }

    // ─── Move stage (Kanban drag) ─────────────────────────────────────────
    public function moveStage(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'stage' => 'required|string|max:30',
        ]);

        $oldStage = $lead->pipeline_stage;
        $lead->update([
            'pipeline_stage'   => $data['stage'],
            'last_activity_at' => now(),
        ]);

        // Log status_change activity
        $lead->activities()->create([
            'type'       => 'status_change',
            'subject'    => 'Этап изменён',
            'body'       => "Лид перемещён из «" . Lead::stageLabel($oldStage ?? 'new') . "» в «" . Lead::stageLabel($data['stage']) . "»",
            'user_id'    => auth()->id(),
            'direction'  => 'internal',
            'is_done'    => true,
            'completed_at' => now(),
        ]);

        AuditLog::record('updated', 'Leads', "Этап лида #{$lead->id} изменён на {$data['stage']}");

        return response()->json(['ok' => true]);
    }

    // ─── Store ────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'nullable|email',
            'message'        => 'nullable|string',
            'status'         => 'required|in:new,in_progress,success,rejected',
            'source'         => 'required|in:website,phone,referral,social,other',
            'interest'       => 'nullable|string',
            'assigned_to'    => 'nullable|exists:users,id',
            'notes'          => 'nullable|string',
            'temperature'    => 'nullable|in:cold,warm,hot',
            'budget_range'   => 'nullable|string|max:60',
            'pipeline_stage' => 'nullable|string|max:30',
            'next_follow_up_at' => 'nullable|date',
            'tags'           => 'nullable|array',
        ]);

        $lead = Lead::create($data);
        AuditLog::record('created', 'Leads', "Лид #{$lead->id} «{$lead->name}» создан", [], $lead);

        return back()->with('success', 'Заявка добавлена.');
    }

    // ─── Update ───────────────────────────────────────────────────────────
    public function update(Request $request, Lead $lead)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'nullable|email',
            'message'        => 'nullable|string',
            'status'         => 'required|in:new,in_progress,success,rejected',
            'source'         => 'required|in:website,phone,referral,social,other',
            'interest'       => 'nullable|string',
            'assigned_to'    => 'nullable|exists:users,id',
            'notes'          => 'nullable|string',
            'temperature'    => 'nullable|in:cold,warm,hot',
            'budget_range'   => 'nullable|string|max:60',
            'pipeline_stage' => 'nullable|string|max:30',
            'score'          => 'nullable|integer|min:0|max:100',
            'next_follow_up_at' => 'nullable|date',
            'tags'           => 'nullable|array',
        ]);

        $old = $lead->only(array_keys($data));
        $lead->update($data);
        AuditLog::record('updated', 'Leads', "Лид #{$lead->id} обновлён", [], $lead, $old, $data);

        return back()->with('success', 'Заявка обновлена.');
    }

    // ─── Destroy ─────────────────────────────────────────────────────────
    public function destroy(Lead $lead)
    {
        Gate::authorize('delete-record');
        AuditLog::record('deleted', 'Leads', "Лид #{$lead->id} «{$lead->name}» удалён");
        $lead->delete();
        return back()->with('success', 'Заявка удалена.');
    }

    // ─── CSV Export ───────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $query = Lead::with('assignee')->latest();

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('source'))      $query->where('source', $request->source);
        if ($request->filled('temperature')) $query->where('temperature', $request->temperature);

        $leads = $query->limit(10000)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="leads_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['ID','Имя','Телефон','Email','Статус','Источник','Этап','Температура','Оценка','Бюджет','Назначен','Заметки','Создана'], ';');
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id,
                    $lead->name,
                    $lead->phone,
                    $lead->email ?? '',
                    Lead::statusLabel($lead->status),
                    $lead->source ?? '',
                    Lead::stageLabel($lead->pipeline_stage ?? ''),
                    $lead->temperature ?? '',
                    $lead->score ?? 0,
                    $lead->budget_range ?? '',
                    $lead->assignee?->name ?? '',
                    $lead->notes ?? '',
                    $lead->created_at->format('d.m.Y H:i'),
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
