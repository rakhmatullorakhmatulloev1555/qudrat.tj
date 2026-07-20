<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PartnerController extends Controller
{
    /** Scan public/images/partners for available logo files */
    private function availableLogos(): array
    {
        $dir = public_path('images/partners');
        if (!is_dir($dir)) return [];
        return collect(scandir($dir))
            ->filter(fn($f) => preg_match('/\.(png|jpg|jpeg|svg|webp)$/i', $f))
            ->values()
            ->map(fn($f) => ['filename' => $f, 'url' => '/images/partners/' . $f])
            ->toArray();
    }

    public function index(Request $request)
    {
        $q = Partner::query();
        if ($request->search) $q->where(function($q2) use ($request) {
            $q2->where('name','like',"%{$request->search}%")->orWhere('country','like',"%{$request->search}%");
        });
        if ($request->type)   $q->where('type', $request->type);
        if ($request->status) $q->where('contract_status', $request->status);
        $partners = $q->orderBy('name')->paginate(20)->withQueryString();
        $stats = [
            'total'       => Partner::count(),
            'active'      => Partner::where('contract_status','active')->count(),
            'buyers'      => Partner::where('type','buyer')->count(),
            'total_volume'=> Partner::sum('annual_volume'),
        ];
        return Inertia::render('Admin/Partners/Index', [
            'partners'       => $partners,
            'stats'          => $stats,
            'filters'        => $request->only('search','type','status'),
            'availableLogos' => $this->availableLogos(),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'             => 'required|string|max:255',
            'country'          => 'required|string|max:100',
            'type'             => 'required|in:buyer,supplier,investor,contractor,government',
            'contact_person'   => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'website'          => 'nullable|string|max:255',
            'logo'             => 'nullable|string|max:255',
            'contract_status'  => 'required|in:active,negotiation,expired,terminated',
            'partnership_since'=> 'nullable|date',
            'annual_volume'    => 'nullable|numeric|min:0',
            'currency'         => 'required|string|max:3',
            'notes'            => 'nullable|string',
            'is_active'        => 'boolean',
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');
        Partner::create($this->validated($request));
        return back()->with('success', 'Партнёр добавлен');
    }

    public function update(Request $request, Partner $partner)
    {
        Gate::authorize('manage');
        $partner->update($this->validated($request));
        return back()->with('success', 'Партнёр обновлён');
    }

    public function destroy(Partner $partner)
    {
        Gate::authorize('delete-record');
        $partner->delete();
        return back()->with('success', 'Партнёр удалён');
    }
}
