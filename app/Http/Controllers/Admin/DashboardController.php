<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Document;
use App\Models\Lead;
use App\Models\MiningShipment;
use App\Models\Partner;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Основные счётчики ──────────────────────────────────────────────
        $stats = [
            'leads_total'          => Lead::count(),
            'leads_new'            => Lead::where('status', 'new')->count(),
            'leads_today'          => Lead::whereDate('created_at', today())->count(),
            'leads_hot'            => Lead::where('temperature', 'hot')->count(),
            'leads_success'        => Lead::where('status', 'success')->count(),
            'clients_total'        => Client::count(),
            'projects_total'       => Project::count(),
            'apartments_available' => Apartment::where('status', 'available')->count(),
            'apartments_sold'      => Apartment::where('status', 'sold')->count(),
            'apartments_reserved'  => Apartment::where('status', 'reserved')->count(),
            'partners_total'       => Partner::where('is_active', true)->count(),
            'documents_expiring'   => Document::where('expires_at', '<=', now()->addDays(30))
                                        ->where('expires_at', '>=', now())->count(),
            'mining_volume_total'  => MiningShipment::sum('volume'),
            'mining_value_total'   => MiningShipment::selectRaw('SUM(volume * price_per_ton) as v')->value('v') ?? 0,
        ];

        // ── Deals статистика ───────────────────────────────────────────────
        $dealsStats = [
            'total'       => Deal::count(),
            'open'        => Deal::where('status', 'open')->count(),
            'won'         => Deal::where('status', 'won')->count(),
            'lost'        => Deal::where('status', 'lost')->count(),
            'total_value' => (float) Deal::where('status', 'open')->sum('amount'),
            'won_value'   => (float) Deal::where('status', 'won')->sum('amount'),
        ];

        // ── График: заявки по месяцам (последние 6 месяцев) ──────────────
        $leadsChart = $this->getMonthlyData(Lead::query(), 'created_at', 6);

        // ── График: партии угля по месяцам ───────────────────────────────
        $miningChart = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);
            return [
                'label'  => $month->isoFormat('MMM YY'),
                'volume' => (float) MiningShipment::whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->sum('volume'),
            ];
        });

        // ── Конверсия заявок (статусы) ────────────────────────────────────
        $conversionData = [
            'new'         => Lead::where('status', 'new')->count(),
            'in_progress' => Lead::where('status', 'in_progress')->count(),
            'success'     => Lead::where('status', 'success')->count(),
            'rejected'    => Lead::where('status', 'rejected')->count(),
        ];

        // ── Распределение по температуре ──────────────────────────────────
        $temperatureData = [
            'cold' => Lead::where(fn($q) => $q->where('temperature','cold')->orWhereNull('temperature'))->count(),
            'warm' => Lead::where('temperature', 'warm')->count(),
            'hot'  => Lead::where('temperature', 'hot')->count(),
        ];

        // ── Воронка по этапам pipeline ────────────────────────────────────
        $stageOrder  = ['new','contacted','qualified','proposal','converted','lost'];
        $stageLabels = [
            'new'       => 'Новый',
            'contacted' => 'Контакт',
            'qualified' => 'Квалифицирован',
            'proposal'  => 'Предложение',
            'converted' => 'Продажа',
            'lost'      => 'Отказ',
        ];
        $stageColors = [
            'new'       => '#6366F1',
            'contacted' => '#3B82F6',
            'qualified' => '#F59E0B',
            'proposal'  => '#8B5CF6',
            'converted' => '#10B981',
            'lost'      => '#EF4444',
        ];
        $stageCounts = Lead::whereNotNull('pipeline_stage')
            ->select('pipeline_stage', DB::raw('count(*) as total'))
            ->groupBy('pipeline_stage')
            ->pluck('total', 'pipeline_stage')
            ->toArray();

        $pipelineFunnel = collect($stageOrder)->map(fn($key) => [
            'key'   => $key,
            'label' => $stageLabels[$key],
            'color' => $stageColors[$key],
            'count' => $stageCounts[$key] ?? 0,
        ])->filter(fn($s) => $s['count'] > 0)->values();

        // ── Топ менеджеры по заявкам ──────────────────────────────────────
        $topManagers = DB::table('leads')
            ->join('users', 'leads.assigned_to', '=', 'users.id')
            ->whereNull('leads.deleted_at')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(*) as leads_count'),
                DB::raw('SUM(CASE WHEN leads.status = "success" THEN 1 ELSE 0 END) as success_count')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('leads_count')
            ->take(5)
            ->get()
            ->map(fn($u) => [
                'id'            => $u->id,
                'name'          => $u->name,
                'leads_count'   => (int) $u->leads_count,
                'success_count' => (int) $u->success_count,
                'conversion'    => $u->leads_count > 0
                    ? round($u->success_count / $u->leads_count * 100)
                    : 0,
            ]);

        // ── Квартиры по типу (комнаты) ────────────────────────────────────
        $apartmentsByRooms = Apartment::select('rooms', DB::raw('count(*) as total'))
            ->groupBy('rooms')
            ->orderBy('rooms')
            ->pluck('total', 'rooms');

        // ── Последние заявки ──────────────────────────────────────────────
        $recentLeads = Lead::with('assignee')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($l) => [
                'id'          => $l->id,
                'name'        => $l->name,
                'phone'       => $l->phone,
                'status'      => $l->status,
                'temperature' => $l->temperature ?? 'cold',
                'source'      => $l->source,
                'interest'    => $l->interest,
                'score'       => $l->score ?? 0,
                'assignee'    => $l->assignee?->only('id','name'),
                'created_at'  => $l->created_at->diffForHumans(),
            ]);

        // ── Activity feed ─────────────────────────────────────────────────
        $activity = collect();
        Lead::latest()->take(5)->get()->each(fn($l) => $activity->push([
            'type'  => 'lead',
            'text'  => "Новая заявка от {$l->name}",
            'time'  => $l->created_at->diffForHumans(),
            'color' => '#6366F1',
        ]));
        MiningShipment::latest()->take(3)->get()->each(fn($s) => $activity->push([
            'type'  => 'mining',
            'text'  => "Партия угля {$s->volume}т — {$s->buyer}",
            'time'  => $s->created_at->diffForHumans(),
            'color' => '#F59E0B',
        ]));

        return Inertia::render('Admin/Dashboard', [
            'stats'            => $stats,
            'deals_stats'      => $dealsStats,
            'recent_leads'     => $recentLeads,
            'leads_chart'      => $leadsChart,
            'mining_chart'     => $miningChart,
            'conversion_data'  => $conversionData,
            'temperature_data' => $temperatureData,
            'pipeline_funnel'  => $pipelineFunnel,
            'top_managers'     => $topManagers,
            'apartments_rooms' => $apartmentsByRooms,
            'activity'         => $activity->values()->take(8),
        ]);
    }

    private function getMonthlyData($query, string $dateField, int $months): \Illuminate\Support\Collection
    {
        return collect(range($months - 1, 0))->map(function ($i) use ($query, $dateField) {
            $month = now()->subMonths($i);
            return [
                'label' => $month->isoFormat('MMM YY'),
                'count' => (clone $query)
                    ->whereYear($dateField, $month->year)
                    ->whereMonth($dateField, $month->month)
                    ->count(),
            ];
        });
    }
}
