<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShowcaseProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

/**
 * CMS-модуль «Знаковые объекты»: блок на /objects + страницы /projects/{slug}.
 */
class ShowcaseProjectController extends Controller
{
    public function index()
    {
        $projects = ShowcaseProject::ordered()->get()->map(fn($p) => [
            'id'            => $p->id,
            'slug'          => $p->slug,
            'name'          => $p->name,
            'accent'        => $p->accent,
            'hero_image'    => $p->hero_image,
            'gallery'       => $p->gallery ?? [],
            'feature_icons' => $p->feature_icons ?? [],
            'content'       => $p->content ?? [],
            'seo'           => $p->seo ?? [],
            'cta_type'      => $p->cta_type,
            'status'        => $p->status,
            'is_featured'   => $p->is_featured,
            'is_visible'    => $p->is_visible,
            'sort_order'    => $p->sort_order,
            'published_at'  => $p->published_at?->format('d.m.Y H:i'),
            'updated_at'    => $p->updated_at?->format('d.m.Y H:i'),
            // Подписанная ссылка предпросмотра (работает и для черновиков)
            'preview_url'   => URL::temporarySignedRoute('projects.show', now()->addHours(24), ['slug' => $p->slug]),
            'public_url'    => route('projects.show', $p->slug),
        ]);

        return Inertia::render('Admin/Showcase/Index', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $this->validated($request);
        $data['slug'] = $data['slug']
            ? ShowcaseProject::makeSlug($data['slug'])
            : ShowcaseProject::makeSlug($data['name']);
        $data['sort_order'] = (ShowcaseProject::max('sort_order') ?? 0) + 1;

        $project = new ShowcaseProject($data);
        $this->applyUploads($request, $project);
        $project->save();

        return back()->with('success', 'Проект создан (черновик).');
    }

    public function update(Request $request, ShowcaseProject $showcaseProject)
    {
        Gate::authorize('manage');

        $data = $this->validated($request);
        if ($data['slug'] && $data['slug'] !== $showcaseProject->slug) {
            $data['slug'] = ShowcaseProject::makeSlug($data['slug'], $showcaseProject->id);
        } else {
            unset($data['slug']);
        }

        $showcaseProject->fill($data);
        $this->applyUploads($request, $showcaseProject);
        $showcaseProject->save();

        return back()->with('success', 'Проект обновлён.');
    }

    public function destroy(ShowcaseProject $showcaseProject)
    {
        Gate::authorize('delete-record');

        $this->deleteUploadedFiles($showcaseProject);
        $showcaseProject->delete();

        return back()->with('success', 'Проект удалён.');
    }

    /** Дублировать проект (копия создаётся черновиком). */
    public function duplicate(ShowcaseProject $showcaseProject)
    {
        Gate::authorize('manage');

        $copy = $showcaseProject->replicate();
        $copy->name         = $showcaseProject->name . ' (копия)';
        $copy->slug         = ShowcaseProject::makeSlug($showcaseProject->slug . '-copy');
        $copy->status       = 'draft';
        $copy->published_at = null;
        $copy->sort_order   = (ShowcaseProject::max('sort_order') ?? 0) + 1;
        $copy->save();

        return back()->with('success', 'Копия создана (черновик).');
    }

    /** Публикация / снятие с публикации. */
    public function togglePublish(ShowcaseProject $showcaseProject)
    {
        Gate::authorize('manage');

        if ($showcaseProject->status === 'published') {
            $showcaseProject->update(['status' => 'draft']);
            return back()->with('success', 'Проект снят с публикации.');
        }

        $showcaseProject->update([
            'status'       => 'published',
            'published_at' => $showcaseProject->published_at ?? now(),
        ]);
        return back()->with('success', 'Проект опубликован.');
    }

    /** В архив / из архива (из архива — в черновик). */
    public function toggleArchive(ShowcaseProject $showcaseProject)
    {
        Gate::authorize('manage');

        $archived = $showcaseProject->status === 'archived';
        $showcaseProject->update(['status' => $archived ? 'draft' : 'archived']);

        return back()->with('success', $archived ? 'Проект возвращён из архива.' : 'Проект в архиве.');
    }

    /** Сортировка: массив id в новом порядке. */
    public function reorder(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        foreach ($data['ids'] as $i => $id) {
            ShowcaseProject::where('id', $id)->update(['sort_order' => $i]);
        }

        return back();
    }

    // ── Внутреннее ──

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255',
            'accent'        => 'required|string|max:20',
            'cta_type'      => 'required|in:apts,contact,mining',
            'is_featured'   => 'boolean',
            'is_visible'    => 'boolean',
            'feature_icons' => 'nullable|array|max:4',
            'feature_icons.*' => 'string|max:30',
            'content'       => 'required|array',
            'content.ru'    => 'required|array',
            'content.tj'    => 'nullable|array',
            'content.en'    => 'nullable|array',
            'seo'           => 'nullable|array',
            'gallery_keep'  => 'nullable|array',       // существующие пути галереи (в нужном порядке)
            'gallery_keep.*'=> 'string|max:500',
        ]);
    }

    /** Обрабатывает загрузку hero / og_image / галереи. */
    private function applyUploads(Request $request, ShowcaseProject $project): void
    {
        $request->validate([
            'hero_file'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'og_file'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'gallery_files'  => 'nullable|array|max:12',
            'gallery_files.*'=> 'image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        if ($request->hasFile('hero_file')) {
            $this->deleteStorageFile($project->hero_image);
            $project->hero_image = '/storage/' . $request->file('hero_file')->store('showcase', 'public');
        }

        if ($request->hasFile('og_file')) {
            $seo = $project->seo ?? [];
            $this->deleteStorageFile($seo['og_image'] ?? null);
            $seo['og_image'] = '/storage/' . $request->file('og_file')->store('showcase', 'public');
            $project->seo = $seo;
        }

        // Галерея: оставленные пути (gallery_keep) + новые файлы.
        // gallery_sync=1 — клиент передал полное состояние галереи (в т.ч. пустое).
        // Удаляем загруженные ранее файлы, которых больше нет в списке.
        if ($request->boolean('gallery_sync') || $request->hasFile('gallery_files')) {
            $keep = array_values($request->input('gallery_keep', []));
            foreach (($project->gallery ?? []) as $old) {
                if (!in_array($old, $keep, true)) {
                    $this->deleteStorageFile($old);
                }
            }
            foreach ($request->file('gallery_files', []) as $file) {
                $keep[] = '/storage/' . $file->store('showcase', 'public');
            }
            $project->gallery = $keep;
        }
    }

    /** Удаляет файл из storage только если он был загружен через админку. */
    private function deleteStorageFile(?string $path): void
    {
        if ($path && str_starts_with($path, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $path));
        }
    }

    private function deleteUploadedFiles(ShowcaseProject $project): void
    {
        $this->deleteStorageFile($project->hero_image);
        $this->deleteStorageFile(data_get($project->seo, 'og_image'));
        foreach (($project->gallery ?? []) as $img) {
            $this->deleteStorageFile($img);
        }
    }
}
