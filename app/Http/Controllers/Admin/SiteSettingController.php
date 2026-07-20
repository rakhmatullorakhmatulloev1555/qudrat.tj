<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::orderBy('group')->orderBy('id')->get();

        $grouped = $settings->groupBy('group')->map(fn($g) => $g->values())->toArray();

        $groupLabels = [
            'general' => 'Общее',
            'contact' => 'Контакты',
            'social'  => 'Социальные сети',
            'seo'     => 'SEO',
        ];

        return Inertia::render('Admin/Settings/Index', [
            'grouped'     => $grouped,
            'groupLabels' => $groupLabels,
        ]);
    }

    public function update(Request $request)
    {
        Gate::authorize('site-settings');

        $data = $request->validate([
            'settings'         => 'required|array',
            'settings.*.key'   => 'required|string|exists:site_settings,key',
            'settings.*.value' => 'nullable|string|max:5000',
        ]);

        foreach ($data['settings'] as $item) {
            SiteSetting::where('key', $item['key'])->update(['value' => $item['value'] ?? '']);
        }

        return back()->with('success', 'Настройки сохранены.');
    }
}
