<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\MiningShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MiningController extends Controller
{
    public function index(Request $request)
    {
        $q = MiningShipment::query();
        if ($request->status) $q->where('status', $request->status);
        if ($request->coal_type) $q->where('coal_type', $request->coal_type);
        $shipments = $q->orderBy('date','desc')->paginate(20)->withQueryString();
        $stats = [
            'total_volume'   => MiningShipment::sum('volume'),
            'total_value'    => MiningShipment::selectRaw('SUM(volume * price_per_ton) as v')->value('v'),
            'this_month'     => MiningShipment::whereMonth('date', now()->month)->sum('volume'),
            'delivered'      => MiningShipment::where('status','delivered')->orWhere('status','paid')->count(),
        ];
        return Inertia::render('Admin/Mining/Index', [
            'shipments' => $shipments,
            'stats'     => $stats,
            'filters'   => $request->only('status','coal_type'),
        ]);
    }
    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'date'=>'required|date','site'=>'required|string|max:255',
            'coal_type'=>'required|in:energy,coking,anthracite',
            'volume'=>'required|numeric|min:0',
            'price_per_ton'=>'nullable|numeric|min:0',
            'currency'=>'required|string|max:3',
            'buyer'=>'nullable|string|max:255',
            'destination'=>'nullable|string|max:255',
            'status'=>'required|in:planned,loading,shipped,delivered,paid',
            'quality_kcal'=>'nullable|integer',
            'notes'=>'nullable|string',
        ]);
        MiningShipment::create($data);
        return back()->with('success', 'Партия добавлена');
    }
    public function update(Request $request, MiningShipment $miningShipment)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'date'=>'required|date','site'=>'required|string|max:255',
            'coal_type'=>'required|in:energy,coking,anthracite',
            'volume'=>'required|numeric|min:0',
            'price_per_ton'=>'nullable|numeric|min:0',
            'currency'=>'required|string|max:3',
            'buyer'=>'nullable|string|max:255',
            'destination'=>'nullable|string|max:255',
            'status'=>'required|in:planned,loading,shipped,delivered,paid',
            'quality_kcal'=>'nullable|integer',
            'notes'=>'nullable|string',
        ]);
        $miningShipment->update($data);
        return back()->with('success', 'Партия обновлена');
    }
    public function destroy(MiningShipment $miningShipment)
    {
        Gate::authorize('delete-record');

        $miningShipment->delete();
        return back()->with('success', 'Партия удалена');
    }

    /**
     * Экспорт партий угля в CSV
     */
    public function export(Request $request)
    {
        $q = MiningShipment::query();
        if ($request->status)    $q->where('status', $request->status);
        if ($request->coal_type) $q->where('coal_type', $request->coal_type);
        $shipments = $q->orderBy('date', 'desc')->limit(10000)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mining_' . now()->format('Y-m-d') . '.csv"',
            'Pragma'              => 'no-cache',
        ];

        $callback = function () use ($shipments) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['ID', 'Дата', 'Место добычи', 'Тип угля', 'Объём (т)', 'Цена/т', 'Валюта', 'Сумма', 'Покупатель', 'Назначение', 'Статус', 'Калорийность', 'Заметки'], ';');

            foreach ($shipments as $s) {
                $coalTypes = ['energy' => 'Энергетический', 'coking' => 'Коксующийся', 'anthracite' => 'Антрацит'];
                $statuses  = ['planned' => 'Планируется', 'loading' => 'Погрузка', 'shipped' => 'Отгружено', 'delivered' => 'Доставлено', 'paid' => 'Оплачено'];

                fputcsv($file, [
                    $s->id,
                    $s->date,
                    $s->site,
                    $coalTypes[$s->coal_type] ?? $s->coal_type,
                    $s->volume,
                    $s->price_per_ton ?? '',
                    $s->currency,
                    $s->price_per_ton ? number_format($s->volume * $s->price_per_ton, 2) : '',
                    $s->buyer ?? '',
                    $s->destination ?? '',
                    $statuses[$s->status] ?? $s->status,
                    $s->quality_kcal ?? '',
                    $s->notes ?? '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
