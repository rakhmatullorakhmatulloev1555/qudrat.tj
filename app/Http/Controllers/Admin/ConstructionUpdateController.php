<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConstructionUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ConstructionUpdateController extends Controller
{
    public function index()
    {
        Gate::authorize('view-crm');

        return Inertia::render('Admin/ConstructionUpdates/Index', [
            'updates' => ConstructionUpdate::orderByDesc('update_date')->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'update_date'  => 'required|date',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:2000',
            'progress'     => 'required|integer|min:0|max:100',
            'is_current'   => 'boolean',
            'is_published' => 'boolean',
        ]);

        if (!empty($data['is_current'])) {
            // Only one record can be current at a time
            ConstructionUpdate::where('is_current', true)->update(['is_current' => false]);
        }

        ConstructionUpdate::create($data);
        Cache::forget('construction_updates');

        return back()->with('success', 'Запись добавлена.');
    }

    public function update(Request $request, ConstructionUpdate $constructionUpdate)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'update_date'  => 'required|date',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:2000',
            'progress'     => 'required|integer|min:0|max:100',
            'is_current'   => 'boolean',
            'is_published' => 'boolean',
        ]);

        if (!empty($data['is_current'])) {
            ConstructionUpdate::where('is_current', true)
                ->where('id', '!=', $constructionUpdate->id)
                ->update(['is_current' => false]);
        }

        $constructionUpdate->update($data);
        Cache::forget('construction_updates');

        return back()->with('success', 'Запись обновлена.');
    }

    public function destroy(ConstructionUpdate $constructionUpdate)
    {
        Gate::authorize('delete-record');

        $constructionUpdate->delete();
        Cache::forget('construction_updates');

        return back()->with('success', 'Запись удалена.');
    }
}
