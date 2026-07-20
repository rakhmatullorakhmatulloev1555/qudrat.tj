<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Apartment::with(['project', 'client', 'images'])->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('rooms')) {
            $query->where('rooms', $request->rooms);
        }
        if ($request->filled('search')) {
            $query->where('number', 'like', "%{$request->search}%");
        }

        return Inertia::render('Admin/Apartments/Index', [
            'apartments' => $query->paginate(25)->withQueryString(),
            'projects'   => Project::select('id', 'name')->get(),
            'clients'    => Client::select('id', 'name', 'phone')->get(),
            'filters'    => $request->only(['project_id', 'status', 'rooms', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'number'     => 'required|string|max:20',
            'floor'      => 'required|integer|min:1',
            'rooms'      => 'required|integer|min:0',
            'area'       => 'required|numeric|min:1',
            'price'      => 'required|numeric|min:0',
            'currency'   => 'required|string|max:10',
            'status'     => 'required|in:available,reserved,sold,soon',
            'finish'     => 'required|in:none,rough,fine,furnished',
            'client_id'  => 'nullable|exists:clients,id',
            'notes'      => 'nullable|string',
            'plan_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if ($request->hasFile('plan_image')) {
            $data['plan_image'] = $request->file('plan_image')->store('apartments/plans', 'public');
        }

        Apartment::create($data);
        $this->clearApartmentCache();
        return back()->with('success', 'Квартира добавлена.');
    }

    public function update(Request $request, Apartment $apartment)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'number'     => 'required|string|max:20',
            'floor'      => 'required|integer|min:1',
            'rooms'      => 'required|integer|min:0',
            'area'       => 'required|numeric|min:1',
            'price'      => 'required|numeric|min:0',
            'currency'   => 'required|string|max:10',
            'status'     => 'required|in:available,reserved,sold,soon',
            'finish'     => 'required|in:none,rough,fine,furnished',
            'client_id'  => 'nullable|exists:clients,id',
            'notes'      => 'nullable|string',
            'plan_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if ($request->hasFile('plan_image')) {
            // Delete old image if exists
            if ($apartment->plan_image) {
                Storage::disk('public')->delete($apartment->plan_image);
            }
            $data['plan_image'] = $request->file('plan_image')->store('apartments/plans', 'public');
        }

        $apartment->update($data);
        $this->clearApartmentCache();
        return back()->with('success', 'Квартира обновлена.');
    }

    public function destroy(Apartment $apartment)
    {
        Gate::authorize('delete-record');

        $apartment->delete();
        $this->clearApartmentCache();
        return back()->with('success', 'Квартира удалена.');
    }

    /* ═══ Массовое изменение ═══ */

    public function bulkUpdate(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'ids'              => 'required|array|min:1',
            'ids.*'            => 'integer|exists:apartments,id',
            'status'           => 'nullable|in:available,reserved,sold,soon',
            'price'            => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:90',
            'floor'            => 'nullable|integer|min:1',
            'block_id'         => 'nullable|integer|exists:blocks,id',
        ]);

        $fields = array_filter([
            'status'   => $data['status'] ?? null,
            'price'    => $data['price'] ?? null,
            'floor'    => $data['floor'] ?? null,
            'block_id' => $data['block_id'] ?? null,
        ], fn($v) => $v !== null);

        $discount = $data['discount_percent'] ?? null;

        if (!$fields && $discount === null) {
            return back()->with('error', 'Не выбрано ни одного изменения.');
        }

        $count = 0;
        foreach (Apartment::whereIn('id', $data['ids'])->get() as $apt) {
            $update = $fields;
            if ($discount !== null) {
                $update['price'] = round((float) $apt->price * (1 - $discount / 100), 2);
            }
            $apt->update($update);
            $count++;
        }

        $this->clearApartmentCache();
        \App\Models\AuditLog::record('updated', 'Apartments',
            "Массовое изменение {$count} квартир",
            ['ids' => $data['ids'], 'changes' => $fields + ($discount !== null ? ['discount_percent' => $discount] : [])]);

        return back()->with('success', "Обновлено квартир: {$count}.");
    }

    /* ═══ Массовый импорт (CSV / JSON) ═══ */

    public function import(Request $request)
    {
        Gate::authorize('manage');

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'file'       => 'required|file|max:20480', // CSV или JSON до 20 МБ
        ]);

        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension());
        $raw  = file_get_contents($file->getRealPath());

        // Разбор: JSON — массив объектов; CSV — первая строка заголовки
        if ($ext === 'json') {
            $rows = json_decode($raw, true);
            if (!is_array($rows)) {
                return back()->with('error', 'Некорректный JSON: ожидается массив объектов.');
            }
        } else {
            $rows = $this->parseCsv($raw);
        }

        $allowed = ['number', 'floor', 'rooms', 'area', 'price', 'currency', 'status', 'finish',
                    'ceiling_height', 'bathrooms', 'balcony', 'view_type', 'notes'];
        $statuses = ['available', 'reserved', 'sold', 'soon'];

        $created = 0; $updated = 0; $skipped = 0;

        foreach ($rows as $row) {
            $row = array_intersect_key(array_change_key_case((array) $row), array_flip($allowed));

            if (empty($row['number']) || !isset($row['floor'], $row['rooms'], $row['area'], $row['price'])) {
                $skipped++;
                continue;
            }
            if (isset($row['status']) && !in_array($row['status'], $statuses)) {
                unset($row['status']);
            }
            if (isset($row['balcony'])) {
                $row['balcony'] = filter_var($row['balcony'], FILTER_VALIDATE_BOOLEAN);
            }

            $apt = Apartment::updateOrCreate(
                ['project_id' => $request->project_id, 'number' => (string) $row['number']],
                $row
            );
            $apt->wasRecentlyCreated ? $created++ : $updated++;
        }

        $this->clearApartmentCache();
        \App\Models\AuditLog::record('imported', 'Apartments',
            "Импорт квартир: создано {$created}, обновлено {$updated}, пропущено {$skipped}",
            ['project_id' => $request->project_id, 'file' => $file->getClientOriginalName()]);

        return back()->with('success', "Импорт завершён: создано {$created}, обновлено {$updated}, пропущено {$skipped}.");
    }

    /** CSV → массив ассоц. строк. Разделитель , или ; определяется по заголовку. */
    private function parseCsv(string $raw): array
    {
        $raw   = preg_replace('/^\xEF\xBB\xBF/', '', $raw); // BOM
        $lines = preg_split('/\r\n|\r|\n/', trim($raw));
        if (count($lines) < 2) return [];

        $delimiter = substr_count($lines[0], ';') > substr_count($lines[0], ',') ? ';' : ',';
        $headers   = array_map(fn($h) => strtolower(trim($h)), str_getcsv($lines[0], $delimiter));

        $rows = [];
        foreach (array_slice($lines, 1) as $line) {
            if (trim($line) === '') continue;
            $values = str_getcsv($line, $delimiter);
            if (count($values) !== count($headers)) continue;
            $rows[] = array_combine($headers, array_map('trim', $values));
        }
        return $rows;
    }

    /* ═══ Галерея фото квартиры ═══ */

    public function storeImages(Request $request, Apartment $apartment)
    {
        Gate::authorize('manage');

        $request->validate([
            'images'   => 'required|array|max:20',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $maxSort    = (int) ($apartment->images()->max('sort') ?? -1);
        $hasPrimary = $apartment->images()->where('is_primary', true)->exists();

        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('apartments/' . $apartment->id, 'public');
            $makePrimary = ! $hasPrimary && $i === 0;
            $apartment->images()->create([
                'path'       => $path,
                'sort'       => ++$maxSort,
                'is_primary' => $makePrimary,
            ]);
            if ($makePrimary) $hasPrimary = true;
        }

        $this->clearApartmentCache();
        return back()->with('success', 'Фотографии загружены.');
    }

    public function destroyImage(Apartment $apartment, ApartmentImage $image)
    {
        Gate::authorize('manage');
        abort_unless($image->apartment_id === $apartment->id, 404);

        Storage::disk('public')->delete($image->path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $apartment->images()->first()?->update(['is_primary' => true]);
        }

        $this->clearApartmentCache();
        return back()->with('success', 'Фото удалено.');
    }

    public function setPrimaryImage(Apartment $apartment, ApartmentImage $image)
    {
        Gate::authorize('manage');
        abort_unless($image->apartment_id === $apartment->id, 404);

        $apartment->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        $this->clearApartmentCache();
        return back()->with('success', 'Главное фото назначено.');
    }

    public function reorderImages(Request $request, Apartment $apartment)
    {
        Gate::authorize('manage');

        $data = $request->validate(['order' => 'required|array', 'order.*' => 'integer']);
        foreach ($data['order'] as $pos => $id) {
            $apartment->images()->where('id', $id)->update(['sort' => $pos]);
        }

        $this->clearApartmentCache();
        return back()->with('success', 'Порядок фото обновлён.');
    }

    private function clearApartmentCache(): void
    {
        Cache::forget('available_apartments');
        Cache::forget('all_apartments_objects');
    }
}
