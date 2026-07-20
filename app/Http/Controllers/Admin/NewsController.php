<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsPost::with('author:id,name')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return Inertia::render('Admin/News/Index', [
            'posts'      => $query->paginate(20)->withQueryString(),
            'categories' => NewsPost::$categories,
            'filters'    => $request->only(['search', 'category']),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'required|string',
            'category'     => 'required|in:news,events,press',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $data['slug']      = NewsPost::makeSlug($data['title']);
        $data['author_id'] = Auth::id();

        if (!empty($data['is_published']) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        NewsPost::create($data);
        Cache::forget('published_news');
        return back()->with('success', 'Новость добавлена.');
    }

    public function update(Request $request, NewsPost $news)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'required|string',
            'category'     => 'required|in:news,events,press',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if (!empty($data['is_published']) && empty($data['published_at'])) {
            $data['published_at'] = $news->published_at ?? now();
        }

        $news->update($data);
        Cache::forget('published_news');
        return back()->with('success', 'Новость обновлена.');
    }

    public function destroy(NewsPost $news)
    {
        Gate::authorize('delete-record');

        $news->delete();
        Cache::forget('published_news');
        return back()->with('success', 'Новость удалена.');
    }
}
