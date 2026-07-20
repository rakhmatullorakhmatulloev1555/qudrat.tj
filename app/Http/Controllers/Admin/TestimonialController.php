<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Testimonials/Index', [
            'testimonials' => Testimonial::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'role'       => 'nullable|string|max:255',
            'company'    => 'nullable|string|max:255',
            'text'       => 'required|string|max:2000',
            'rating'     => 'required|integer|min:1|max:5',
            'is_active'  => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        Testimonial::create($data);
        Cache::forget('home_testimonials');
        return back()->with('success', 'Отзыв добавлен.');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'role'       => 'nullable|string|max:255',
            'company'    => 'nullable|string|max:255',
            'text'       => 'required|string|max:2000',
            'rating'     => 'required|integer|min:1|max:5',
            'is_active'  => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $testimonial->update($data);
        Cache::forget('home_testimonials');
        return back()->with('success', 'Отзыв обновлён.');
    }

    public function destroy(Testimonial $testimonial)
    {
        Gate::authorize('delete-record');

        $testimonial->delete();
        Cache::forget('home_testimonials');
        return back()->with('success', 'Отзыв удалён.');
    }
}
