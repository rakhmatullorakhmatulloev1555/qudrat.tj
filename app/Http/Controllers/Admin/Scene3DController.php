<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentScene;
use App\Models\InteriorStyle;
use App\Models\Material;
use App\Models\SceneAsset;
use App\Services\Scene3DStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

/**
 * Админ-раздел «3D Квартиры» (Этап 2).
 * MVP управления: список квартир + включение сцены + сводка библиотек.
 * CRUD для комнат/ассетов/hotspots/маршрутов строится по этому же паттерну.
 */
class Scene3DController extends Controller
{
    public function index(Request $request)
    {
        $apartments = Apartment::with(['project:id,name,slug', 'scene'])
            ->select('id', 'project_id', 'number', 'rooms', 'area', 'floor', 'status')
            ->when($request->search, fn($q, $s) => $q->where('number', 'like', "%{$s}%"))
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn($a) => [
                'id'        => $a->id,
                'number'    => $a->number,
                'rooms'     => $a->rooms,
                'area'      => (float) $a->area,
                'floor'     => $a->floor,
                'project'   => $a->project?->name,
                'slug'      => $a->project?->slug,
                'enabled'   => (bool) $a->scene?->is_enabled,
                'has_scene' => (bool) $a->scene,
            ]);

        return Inertia::render('Admin/Scene3D/Index', [
            'apartments' => $apartments,
            'filters'    => $request->only('search'),
            'stats'      => [
                'styles'    => InteriorStyle::count(),
                'materials' => Material::count(),
                'models'    => SceneAsset::kind('model')->count(),
                'enabled'   => ApartmentScene::where('is_enabled', true)->count(),
            ],
        ]);
    }

    /** Включить/выключить 3D-сцену для квартиры (создаёт структуру хранения). */
    public function toggle(Apartment $apartment)
    {
        Gate::authorize('manage');

        $scene = ApartmentScene::firstOrCreate(
            ['apartment_id' => $apartment->id],
            ['is_enabled' => false, 'default_style_slug' => 'modern-classic']
        );

        $scene->is_enabled = ! $scene->is_enabled;
        $scene->save();

        if ($scene->is_enabled) {
            Scene3DStorage::ensureStructure($scene);
        }

        return back()->with('success', $scene->is_enabled ? '3D-сцена включена.' : '3D-сцена выключена.');
    }
}
