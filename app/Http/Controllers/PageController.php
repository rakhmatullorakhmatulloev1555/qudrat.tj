<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Apartment;
use App\Models\ConstructionUpdate;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\Testimonial;
use App\Models\NewsPost;
use App\Models\Project;
use App\Models\ShowcaseProject;

class PageController extends Controller
{
    public function home(): Response
    {
        // Кешируем на 1 час — не долбим БД при каждом визите
        $apartments = Cache::remember('available_apartments', 3600, fn() =>
            Apartment::where('status', 'available')
                ->select('id', 'rooms', 'area', 'floor', 'price', 'currency', 'finish', 'status')
                ->orderBy('rooms')
                ->orderBy('price')
                ->limit(24)
                ->get()
        );

        // Отзывы из БД (кеш 30 мин)
        $testimonials = Cache::remember('home_testimonials', 1800, fn() =>
            Testimonial::active()
                ->select('id', 'name', 'role', 'company', 'text', 'rating')
                ->limit(6)
                ->get()
                ->map(fn($t) => [
                    'id'      => $t->id,
                    'name'    => $t->name,
                    'role'    => $t->role,
                    'company' => $t->company,
                    'text'    => $t->text,
                    'rating'  => $t->rating,
                    'initials'=> $t->initials,
                ])
        );

        // Партнёры с логотипами для главной страницы (кеш 1 час)
        $homePartners = Cache::remember('home_partners', 3600, fn() =>
            Partner::where('is_active', true)
                ->whereNotNull('logo')
                ->orderBy('name')
                ->get(['id', 'name', 'logo'])
        );

        return Inertia::render('Home', compact('apartments', 'testimonials', 'homePartners'));
    }

    public function about(): Response
    {
        return Inertia::render('About');
    }

    public function objects(): Response
    {
        $apartments = Cache::remember('all_apartments_objects', 1800, fn() =>
            Apartment::with('project:id,name,slug')
                ->select('id', 'project_id', 'number', 'rooms', 'area', 'floor',
                         'price', 'currency', 'status', 'finish', 'plan_image')
                ->orderBy('rooms')
                ->orderBy('floor')
                ->orderBy('price')
                ->limit(500)
                ->get()
        );

        $available = $apartments->where('status', 'available');

        $stats = [
            'total'     => $apartments->count(),
            'available' => $available->count(),
            'reserved'  => $apartments->where('status', 'reserved')->count(),
            'sold'      => $apartments->where('status', 'sold')->count(),
            'min_price' => (int) ($available->min('price') ?? 0),
            'max_price' => (int) ($available->max('price') ?? 0),
            'min_area'  => (float) ($apartments->min('area') ?? 0),
            'max_area'  => (float) ($apartments->max('area') ?? 0),
            'floors'    => (int) ($apartments->max('floor') ?? 0),
        ];

        // Construction progress data (merged from Progress page)
        $updates = Cache::remember('construction_updates', 1800, fn() =>
            ConstructionUpdate::published()
                ->orderByDesc('update_date')
                ->limit(12)
                ->get(['id', 'update_date', 'title', 'description', 'progress', 'is_current'])
                ->map(fn($u) => [
                    'id'          => $u->id,
                    'date'        => $u->update_date->format('d.m.Y'),
                    'title'       => $u->title,
                    'description' => $u->description,
                    'progress'    => $u->progress,
                    'is_current'  => $u->is_current,
                ])
        );

        $overallProgress = $updates->firstWhere('is_current', true)['progress']
            ?? ($updates->first()['progress'] ?? 0);

        $locale = app()->getLocale();
        $featuredProjects = ShowcaseProject::published()->visible()->ordered()
            ->where('is_featured', true)
            ->get()
            ->map(fn($p) => $p->toCard($locale));

        return Inertia::render('Objects', [
            'apartments'       => $apartments,
            'stats'            => $stats,
            'updates'          => $updates,
            'overallProgress'  => $overallProgress,
            'featuredProjects' => $featuredProjects,
        ]);
    }

    /**
     * Детальная страница знакового проекта: /projects/{slug}
     * Контент управляется через CMS (админка → «Знаковые объекты»).
     * Черновики и архив доступны только по подписанной preview-ссылке из админки.
     */
    public function projectShow(Request $request, string $slug): Response
    {
        $project = ShowcaseProject::where('slug', $slug)->firstOrFail();

        $isPreview = false;
        if ($project->status !== 'published') {
            abort_unless($request->hasValidSignature(), 404);
            $isPreview = true;
        }

        $locale = app()->getLocale();

        $others = ShowcaseProject::published()->visible()->ordered()
            ->where('id', '!=', $project->id)
            ->limit(3)
            ->get()
            ->map(fn($p) => $p->toCard($locale));

        return Inertia::render('Projects/Show', [
            'project'   => $project->toPage($locale),
            'others'    => $others,
            'isPreview' => $isPreview,
        ]);
    }

    /**
     * Детальная страница квартиры: /objects/{projectSlug}/{apartment}
     */
    public function apartmentShow(Request $request, string $projectSlug, Apartment $apartment): Response
    {
        $apartment->load(['project:id,name,slug,city,address', 'images']);
        $project = $apartment->project;

        // Галерея фото: главное фото первым, затем по порядку
        $gallery = $apartment->images
            ->sortByDesc('is_primary')
            ->values()
            ->map(fn($img) => '/storage/' . ltrim($img->path, '/'))
            ->all();

        // Канонический URL: если slug проекта в URL не совпадает — 301 на правильный
        // (обрабатывается на уровне контроллера — см. роут-редирект ниже через canonical prop)
        $canonicalSlug = $project?->slug ?: 'project';

        // Похожие квартиры: сначала из этого же проекта, затем по площади
        $similar = Apartment::with('project:id,slug')
            ->where('id', '!=', $apartment->id)
            ->where('project_id', $apartment->project_id)
            ->orderByRaw("CASE WHEN status = 'available' THEN 0 ELSE 1 END")
            ->orderByRaw('ABS(area - ?)', [$apartment->area])
            ->limit(4)
            ->get(['id', 'project_id', 'number', 'rooms', 'area', 'floor', 'price', 'currency', 'status', 'finish', 'plan_image']);

        // Добор по площади из других проектов, если в этом проекте мало квартир
        if ($similar->count() < 4) {
            $extra = Apartment::with('project:id,slug')
                ->where('id', '!=', $apartment->id)
                ->where('project_id', '!=', $apartment->project_id)
                ->whereNotIn('id', $similar->pluck('id'))
                ->orderByRaw('ABS(area - ?)', [$apartment->area])
                ->limit(4 - $similar->count())
                ->get(['id', 'project_id', 'number', 'rooms', 'area', 'floor', 'price', 'currency', 'status', 'finish', 'plan_image']);
            $similar = $similar->concat($extra);
        }

        return Inertia::render('Apartments/Show', [
            'apartment' => [
                'id'         => $apartment->id,
                'number'     => $apartment->number,
                'floor'      => $apartment->floor,
                'rooms'      => $apartment->rooms,
                'area'       => (float) $apartment->area,
                'price'      => (float) $apartment->price,
                'currency'   => $apartment->currency,
                'status'     => $apartment->status,
                'finish'     => $apartment->finish,
                'plan_image' => $apartment->plan_image,
                'images'     => $gallery,
                'notes'      => $apartment->notes,
            ],
            'project' => $project ? [
                'name'    => $project->name,
                'slug'    => $project->slug,
                'city'    => $project->city,
                'address' => $project->address,
            ] : null,
            'similar' => $similar->map(fn($a) => [
                'id'         => $a->id,
                'number'     => $a->number,
                'rooms'      => $a->rooms,
                'area'       => (float) $a->area,
                'floor'      => $a->floor,
                'price'      => (float) $a->price,
                'currency'   => $a->currency,
                'status'     => $a->status,
                'finish'     => $a->finish,
                'plan_image' => $a->plan_image,
                'project_slug' => $a->project?->slug ?: $canonicalSlug,
            ]),
            'canonicalSlug' => $canonicalSlug,
        ]);
    }

    /**
     * 3D-просмотр квартиры: /objects/{projectSlug}/{apartment}/3d
     */
    public function apartment3d(string $projectSlug, Apartment $apartment): Response
    {
        $apartment->load(['project:id,name,slug', 'scene']);
        $scene = $apartment->scene;

        return Inertia::render('Apartments/Tour', [
            'apartment' => [
                'id'       => $apartment->id,
                'number'   => $apartment->number,
                'rooms'    => $apartment->rooms,
                'area'     => (float) $apartment->area,
                'floor'    => $apartment->floor,
                'price'    => (float) $apartment->price,
                'currency' => $apartment->currency,
                'status'   => $apartment->status,
                'finish'   => $apartment->finish,
                'ceiling_height' => $scene?->ceiling_height ? (float) $scene->ceiling_height : 2.8,
            ],
            'project' => $apartment->project ? [
                'name' => $apartment->project->name,
                'slug' => $apartment->project->slug,
            ] : null,
        ]);
    }

    public function investors(): Response
    {
        return Inertia::render('Investors');
    }

    public function services(): Response
    {
        return Inertia::render('Services');
    }

    public function bms(): Response
    {
        return Inertia::render('Bms');
    }

    public function smartHome(): Response
    {
        return Inertia::render('SmartHome');
    }

    public function contacts(): Response
    {
        return Inertia::render('Contacts');
    }

    public function news(): Response
    {
        $posts = Cache::remember('published_news', 900, fn() =>
            NewsPost::published()
                ->select('id', 'title', 'slug', 'excerpt', 'category', 'image', 'published_at')
                ->limit(30)
                ->get()
                ->map(fn($p) => [
                    'id'             => $p->id,
                    'title'          => $p->title,
                    'slug'           => $p->slug,
                    'excerpt'        => $p->excerpt,
                    'category'       => $p->category,
                    'category_label' => NewsPost::$categories[$p->category] ?? $p->category,
                    'image'          => $p->image,
                    'date'           => $p->published_at?->format('d.m.Y'),
                    'date_iso'       => $p->published_at?->toDateString(),
                ])
        );

        return Inertia::render('News', ['posts' => $posts]);
    }

    /**
     * Детальная страница новости.
     */
    public function newsShow(string $slug): Response
    {
        $post = NewsPost::published()->where('slug', $slug)->firstOrFail();

        $more = NewsPost::published()
            ->where('id', '!=', $post->id)
            ->select('id', 'title', 'slug', 'category', 'image', 'published_at')
            ->limit(3)
            ->get()
            ->map(fn($p) => [
                'title'          => $p->title,
                'slug'           => $p->slug,
                'category_label' => NewsPost::$categories[$p->category] ?? $p->category,
                'image'          => $p->image,
                'date'           => $p->published_at?->format('d.m.Y'),
            ]);

        return Inertia::render('News/Show', [
            'post' => [
                'title'          => $post->title,
                'excerpt'        => $post->excerpt,
                'body'           => $post->body,
                'category'       => $post->category,
                'category_label' => NewsPost::$categories[$post->category] ?? $post->category,
                'image'          => $post->image,
                'date'           => $post->published_at?->format('d.m.Y'),
                'date_iso'       => $post->published_at?->toDateString(),
            ],
            'more' => $more,
        ]);
    }

    public function mining(): Response
    {
        return Inertia::render('Mining');
    }

    public function partners(): Response
    {
        $partners = Cache::remember('public_partners', 1800, fn() =>
            Partner::where('is_active', true)
                ->whereNotNull('logo')
                ->orderBy('name')
                ->get(['id', 'name', 'country', 'type', 'logo', 'website'])
        );

        $allPartners = Cache::remember('public_partners_all', 1800, fn() =>
            Partner::where('is_active', true)
                ->orderBy('country')
                ->orderBy('name')
                ->get(['id', 'name', 'country', 'type', 'logo'])
        );

        return Inertia::render('Partners', [
            'logoPartners' => $partners,
            'allPartners'  => $allPartners,
        ]);
    }

    public function progress(): Response
    {
        $updates = Cache::remember('construction_updates', 1800, fn() =>
            ConstructionUpdate::published()
                ->orderByDesc('update_date')
                ->limit(12)
                ->get(['id', 'update_date', 'title', 'description', 'progress', 'is_current'])
                ->map(fn($u) => [
                    'id'          => $u->id,
                    'date'        => $u->update_date->format('d.m.Y'),
                    'title'       => $u->title,
                    'description' => $u->description,
                    'progress'    => $u->progress,
                    'is_current'  => $u->is_current,
                ])
        );

        $overallProgress = $updates->firstWhere('is_current', true)['progress']
            ?? ($updates->first()['progress'] ?? 0);

        return Inertia::render('Progress', [
            'updates'         => $updates,
            'overallProgress' => $overallProgress,
        ]);
    }

    public function privacy(): Response
    {
        return Inertia::render('Privacy');
    }

    public function terms(): Response
    {
        return Inertia::render('Terms');
    }

    /**
     * Форма обратной связи — сохраняем как Lead + сбрасываем кеш
     */
    public function career(): Response
    {
        return Inertia::render('Career');
    }

    public function careerApply(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:30',
            'email'    => 'nullable|email|max:255',
            'position' => 'nullable|string|max:255',
            'message'  => 'nullable|string|max:3000',
        ]);

        Lead::create([
            'name'    => $validated['name'],
            'phone'   => $validated['phone'],
            'email'   => $validated['email'] ?? null,
            'message' => "[КАРЬЕРА] Желаемая должность: " . ($validated['position'] ?? '—') . "\n\n" . ($validated['message'] ?? ''),
            'source'  => 'career',
            'status'  => 'new',
        ]);

        return back()->with('success', true);
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:30',
            'email'   => 'nullable|email|max:255',
            'message' => 'nullable|string|max:2000',
        ]);

        Lead::create([
            'name'    => $validated['name'],
            'phone'   => $validated['phone'],
            'email'   => $validated['email'] ?? null,
            'message' => $validated['message'] ?? null,
            'source'  => 'website',
            'status'  => 'new',
        ]);

        return back()->with('success', true);
    }

    /**
     * Онлайн-бронирование квартиры — сохраняем как Lead с деталями
     */
    public function reserve(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'nullable|email|max:255',
            'citizenship'    => 'nullable|string|max:100',
            'rooms'          => 'nullable|string|max:20',
            'budget_from'    => 'nullable|numeric|min:0',
            'budget_to'      => 'nullable|numeric|min:0',
            'finish'         => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|max:100',
            'apartment_id'   => 'nullable|integer',
            'apartment_number' => 'nullable|string|max:20',
        ]);

        $note = "Онлайн-бронирование квартиры\n";
        if (!empty($validated['apartment_number'])) $note .= "Квартира: №{$validated['apartment_number']}"
            . (!empty($validated['apartment_id']) ? " (ID {$validated['apartment_id']})" : '') . "\n";
        if (!empty($validated['citizenship']))    $note .= "Гражданство: {$validated['citizenship']}\n";
        if (!empty($validated['rooms']))          $note .= "Комнат: {$validated['rooms']}\n";
        if (!empty($validated['budget_from']))    $note .= "Бюджет от: \${$validated['budget_from']}\n";
        if (!empty($validated['budget_to']))      $note .= "Бюджет до: \${$validated['budget_to']}\n";
        if (!empty($validated['finish']))         $note .= "Отделка: {$validated['finish']}\n";
        if (!empty($validated['payment_method'])) $note .= "Оплата: {$validated['payment_method']}\n";

        Lead::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'] ?? null,
            'source'   => 'website',
            'interest' => 'apartment',
            'status'   => 'new',
            'notes'    => $note,
        ]);

        return back()->with('reserved', true);
    }

    /**
     * B2B заявка на поставку угля
     */
    public function miningContact(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:30',
            'email'      => 'nullable|email|max:255',
            'volume'     => 'nullable|string|max:100',
            'coal_type'  => 'nullable|string|max:50',
            'message'    => 'nullable|string|max:2000',
        ]);

        Lead::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'] ?? null,
            'source'   => 'website',
            'interest' => 'mining',
            'status'   => 'new',
            'notes'    => "B2B заявка на уголь\n"
                        . "Объём: " . ($validated['volume'] ?? '—') . "\n"
                        . "Тип угля: " . ($validated['coal_type'] ?? '—') . "\n"
                        . "Комментарий: " . ($validated['message'] ?? '—'),
        ]);

        return back()->with('mining_sent', true);
    }

    /**
     * Подписка на рассылку
     */
    public function newsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Сохраняем как лид с источником newsletter
        Lead::firstOrCreate(
            ['email' => $request->email, 'source' => 'newsletter'],
            [
                'name'   => $request->email,
                'phone'  => '',
                'source' => 'newsletter',
                'status' => 'new',
                'notes'  => 'Подписка на рассылку',
            ]
        );

        return back()->with('newsletter_sent', true);
    }

    /**
     * Страница регистрации клиента
     */
    public function register(): Response
    {
        return Inertia::render('Register');
    }

    /**
     * Сохранение регистрации клиента
     */
    public function storeRegister(Request $request)
    {
        $validated = $request->validate([
            'interest'       => 'required|string|max:50',
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'nullable|email|max:255',
            'rooms'          => 'nullable|string|max:20',
            'budget'         => 'nullable|string|max:100',
            'floor_pref'     => 'nullable|string|max:50',
            'finish'         => 'nullable|string|max:50',
            'timeline'       => 'nullable|string|max:100',
            'invest_amount'  => 'nullable|string|max:100',
            'invest_goal'    => 'nullable|string|max:100',
            'contact_method' => 'nullable|string|max:50',
            'comment'        => 'nullable|string|max:1000',
        ]);

        $interestLabels = [
            'apartment'   => 'Покупка квартиры',
            'invest'      => 'Инвестиции в недвижимость',
            'commercial'  => 'Коммерческая недвижимость',
            'mining'      => 'Партнёрство (горнодобыча)',
        ];

        $note = "Регистрация клиента\n";
        $note .= "Интерес: " . ($interestLabels[$validated['interest']] ?? $validated['interest']) . "\n";
        if (!empty($validated['rooms']))          $note .= "Комнат: {$validated['rooms']}\n";
        if (!empty($validated['budget']))         $note .= "Бюджет: {$validated['budget']}\n";
        if (!empty($validated['floor_pref']))     $note .= "Этаж: {$validated['floor_pref']}\n";
        if (!empty($validated['finish']))         $note .= "Отделка: {$validated['finish']}\n";
        if (!empty($validated['timeline']))       $note .= "Срок: {$validated['timeline']}\n";
        if (!empty($validated['invest_amount']))  $note .= "Сумма инвестиций: {$validated['invest_amount']}\n";
        if (!empty($validated['invest_goal']))    $note .= "Цель инвестиций: {$validated['invest_goal']}\n";
        if (!empty($validated['contact_method'])) $note .= "Способ связи: {$validated['contact_method']}\n";
        if (!empty($validated['comment']))        $note .= "Комментарий: {$validated['comment']}\n";

        Lead::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'] ?? null,
            'source'   => 'website',
            'interest' => $validated['interest'],
            'status'   => 'new',
            'notes'    => $note,
        ]);

        return back()->with('registered', true);
    }

    /**
     * Sitemap XML для SEO
     */
    public function sitemap()
    {
        $locales = ['ru', 'en', 'tj'];
        $baseUrl = rtrim(config('app.url'), '/');

        $paths = [
            ['path' => '/',          'priority' => '1.0', 'changefreq' => 'daily'],
            ['path' => '/objects',   'priority' => '0.9', 'changefreq' => 'daily'],
            ['path' => '/mining',    'priority' => '0.8', 'changefreq' => 'weekly'],
            ['path' => '/partners',  'priority' => '0.7', 'changefreq' => 'monthly'],
            ['path' => '/about',     'priority' => '0.7', 'changefreq' => 'monthly'],
            ['path' => '/services',  'priority' => '0.7', 'changefreq' => 'monthly'],
            ['path' => '/contacts',  'priority' => '0.6', 'changefreq' => 'monthly'],
            ['path' => '/news',      'priority' => '0.6', 'changefreq' => 'weekly'],
            ['path' => '/privacy',   'priority' => '0.3', 'changefreq' => 'yearly'],
            ['path' => '/terms',     'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        // Locale → hreflang tag value
        $hreflangMap = ['ru' => 'ru', 'en' => 'en', 'tj' => 'tg'];

        $today  = now()->toDateString();
        $xml    = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml   .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml   .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($paths as $page) {
            $canonical = $baseUrl . $page['path'];

            $xml .= "  <url>\n";
            $xml .= "    <loc>{$canonical}</loc>\n";

            // hreflang alternate links (x-default = ru)
            $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$canonical}\"/>\n";
            foreach ($locales as $locale) {
                $tag = $hreflangMap[$locale];
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$tag}\" href=\"{$canonical}?lang={$locale}\"/>\n";
            }

            $xml .= "    <changefreq>{$page['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$page['priority']}</priority>\n";
            $xml .= "    <lastmod>{$today}</lastmod>\n";
            $xml .= "  </url>\n";
        }

        // Опубликованные новости — динамические URL для индексации
        foreach (NewsPost::published()->select('slug', 'published_at')->limit(500)->get() as $post) {
            $loc = $baseUrl . '/news/' . $post->slug;
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.5</priority>\n";
            $xml .= "    <lastmod>" . $post->published_at->toDateString() . "</lastmod>\n";
            $xml .= "  </url>\n";
        }

        // Знаковые объекты (CMS)
        foreach (ShowcaseProject::published()->visible()->get(['slug', 'updated_at']) as $sp) {
            $loc = $baseUrl . '/projects/' . $sp->slug;
            $xml .= "  <url>\n    <loc>{$loc}</loc>\n    <changefreq>monthly</changefreq>\n    <priority>0.7</priority>\n    <lastmod>" . $sp->updated_at->toDateString() . "</lastmod>\n  </url>\n";
        }

        // Интерактивный выбор квартиры: генплан → корпуса → этажи → квартиры
        $projects = Project::where('is_published', true)
            ->whereHas('blocks', fn($q) => $q->where('is_published', true))
            ->with(['blocks' => fn($q) => $q->where('is_published', true)])
            ->get(['id', 'slug', 'updated_at']);

        foreach ($projects as $proj) {
            $projUrl = $baseUrl . '/complex/' . $proj->slug;
            $xml .= "  <url>\n    <loc>{$projUrl}</loc>\n    <changefreq>daily</changefreq>\n    <priority>0.8</priority>\n    <lastmod>" . $proj->updated_at->toDateString() . "</lastmod>\n  </url>\n";

            foreach ($proj->blocks as $block) {
                $blockUrl = $projUrl . '/' . $block->slug;
                $xml .= "  <url>\n    <loc>{$blockUrl}</loc>\n    <changefreq>daily</changefreq>\n    <priority>0.7</priority>\n    <lastmod>" . $block->updated_at->toDateString() . "</lastmod>\n  </url>\n";

                foreach (range(1, $block->floors_total) as $n) {
                    $xml .= "  <url>\n    <loc>{$blockUrl}/floor-{$n}</loc>\n    <changefreq>daily</changefreq>\n    <priority>0.6</priority>\n    <lastmod>{$today}</lastmod>\n  </url>\n";
                }
            }
        }

        // Карточки квартир
        foreach (Apartment::with('project:id,slug')->select('id', 'project_id', 'updated_at')->limit(5000)->get() as $apt) {
            if (!$apt->project?->slug) continue;
            $loc = $baseUrl . '/objects/' . $apt->project->slug . '/' . $apt->id;
            $xml .= "  <url>\n    <loc>{$loc}</loc>\n    <changefreq>weekly</changefreq>\n    <priority>0.6</priority>\n    <lastmod>" . $apt->updated_at->toDateString() . "</lastmod>\n  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
}
