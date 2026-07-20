<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GalleryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Gallery/Index', [
            'images'     => GalleryImage::orderBy('category')->orderBy('sort_order')->orderBy('id')->get(),
            'categories' => GalleryImage::$categories,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'title'      => 'nullable|string|max:255',
            'alt'        => 'nullable|string|max:255',
            'image'      => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'category'   => 'required|in:building,interior,progress,events',
            'is_active'  => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        GalleryImage::create([
            'title'      => $data['title'] ?? null,
            'alt'        => $data['alt'] ?? null,
            'image_path' => '/storage/' . $path,
            'category'   => $data['category'],
            'is_active'  => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        Cache::forget('home_gallery');
        return back()->with('success', 'Изображение добавлено.');
    }

    public function update(Request $request, GalleryImage $galleryImage)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'title'      => 'nullable|string|max:255',
            'alt'        => 'nullable|string|max:255',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'category'   => 'required|in:building,interior,progress,events',
            'is_active'  => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            // Remove old image
            $old = str_replace('/storage/', '', $galleryImage->image_path);
            Storage::disk('public')->delete($old);
            $data['image_path'] = '/storage/' . $request->file('image')->store('gallery', 'public');
        }

        unset($data['image']);
        $galleryImage->update($data);
        Cache::forget('home_gallery');
        return back()->with('success', 'Изображение обновлено.');
    }

    public function destroy(GalleryImage $galleryImage)
    {
        Gate::authorize('delete-record');

        $old = str_replace('/storage/', '', $galleryImage->image_path);
        Storage::disk('public')->delete($old);
        $galleryImage->delete();
        Cache::forget('home_gallery');
        return back()->with('success', 'Изображение удалено.');
    }
}
