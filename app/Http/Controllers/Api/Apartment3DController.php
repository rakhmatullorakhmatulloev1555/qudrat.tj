<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentScene;
use App\Models\InteriorStyle;
use Illuminate\Http\JsonResponse;

/**
 * Публичный REST API системы «3D Квартиры».
 * Отдаёт данные сцены фронтенду (Three.js/TresJS). Только чтение, кешируется.
 */
class Apartment3DController extends Controller
{
    /** GET /api/apartments/{apartment}/3d — сводка сцены. */
    public function scene(Apartment $apartment): JsonResponse
    {
        $scene = $apartment->relationLoaded('scene')
            ? $apartment->scene
            : ApartmentScene::with(['rooms', 'assets', 'hotspots', 'routes'])
                ->where('apartment_id', $apartment->id)->first();

        return response()->json([
            'apartment_id' => $apartment->id,
            'enabled'      => (bool) ($scene?->is_enabled),
            'default_style'=> $scene?->default_style_slug ?? 'modern-classic',
            'ceiling_height' => $scene?->ceiling_height,
            'rooms'        => $this->rooms($apartment)->getData()->rooms ?? [],
            'has_models'   => $scene ? $scene->assets->where('kind', 'model')->isNotEmpty() : false,
            'source'       => $scene && $scene->assets->where('kind', 'model')->isNotEmpty() ? 'gltf' : 'procedural',
        ]);
    }

    /** GET /api/apartments/{apartment}/styles — активные стили. */
    public function styles(Apartment $apartment): JsonResponse
    {
        $styles = InteriorStyle::active()->get()->map(fn($s) => [
            'slug'       => $s->slug,
            'name'       => $s->name,
            'accent'     => $s->accent_hex,
            'background'  => $s->background_hex,
            'materials'  => $s->materials,
            'lighting'   => $s->lighting,
            'hdri'       => $s->hdri_path,
        ]);

        return response()->json(['styles' => $styles]);
    }

    /** GET /api/apartments/{apartment}/rooms — комнаты сцены. */
    public function rooms(Apartment $apartment): JsonResponse
    {
        $scene = ApartmentScene::with('rooms')->where('apartment_id', $apartment->id)->first();
        $rooms = ($scene?->rooms ?? collect())->map(fn($r) => [
            'slug'   => $r->slug,
            'name'   => $r->name,
            'icon'   => $r->icon,
            'camera' => ['pos' => $r->camera_pos, 'target' => $r->camera_target],
            'model'  => $r->model_asset_id,
        ]);

        return response()->json(['rooms' => $rooms]);
    }

    /** GET /api/apartments/{apartment}/hotspots — точки интереса. */
    public function hotspots(Apartment $apartment): JsonResponse
    {
        $scene = ApartmentScene::with('hotspots')->where('apartment_id', $apartment->id)->first();
        $hotspots = ($scene?->hotspots ?? collect())->map(fn($h) => [
            'name'        => $h->name,
            'description' => $h->description,
            'position'    => $h->position,
            'icon'        => $h->icon,
            'target'      => $h->target_room_slug,
        ]);

        return response()->json(['hotspots' => $hotspots]);
    }

    /** GET /api/apartments/{apartment}/tour — маршруты камеры. */
    public function tour(Apartment $apartment): JsonResponse
    {
        $scene = ApartmentScene::with('routes')->where('apartment_id', $apartment->id)->first();
        $route = ($scene?->routes ?? collect())->firstWhere('is_default', true)
            ?? ($scene?->routes ?? collect())->first();

        return response()->json([
            'name'      => $route?->name,
            'waypoints' => $route?->waypoints ?? [],
        ]);
    }
}
