<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::withCount('apartments')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $query->paginate(15)->withQueryString(),
            'filters'  => $request->only(['status', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:residential,commercial,mixed',
            'status'           => 'required|in:planned,under_construction,on_sale,completed',
            'class'            => 'required|in:economy,comfort,business,premium',
            'city'             => 'required|string',
            'address'          => 'nullable|string',
            'description'      => 'nullable|string',
            'price_from'       => 'nullable|numeric|min:0',
            'price_to'         => 'nullable|numeric|min:0',
            'currency'         => 'required|string|max:10',
            'floors_count'     => 'nullable|integer|min:1',
            'apartments_count' => 'nullable|integer|min:1',
            'completion_year'  => 'nullable|integer|min:2000',
            'is_published'     => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . time();
        Project::create($data);
        return back()->with('success', 'Проект добавлен.');
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:residential,commercial,mixed',
            'status'           => 'required|in:planned,under_construction,on_sale,completed',
            'class'            => 'required|in:economy,comfort,business,premium',
            'city'             => 'required|string',
            'address'          => 'nullable|string',
            'description'      => 'nullable|string',
            'price_from'       => 'nullable|numeric|min:0',
            'price_to'         => 'nullable|numeric|min:0',
            'currency'         => 'required|string|max:10',
            'floors_count'     => 'nullable|integer|min:1',
            'apartments_count' => 'nullable|integer|min:1',
            'completion_year'  => 'nullable|integer|min:2000',
            'is_published'     => 'boolean',
        ]);

        $project->update($data);
        return back()->with('success', 'Проект обновлён.');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete-record');

        $project->delete();
        return back()->with('success', 'Проект удалён.');
    }
}
