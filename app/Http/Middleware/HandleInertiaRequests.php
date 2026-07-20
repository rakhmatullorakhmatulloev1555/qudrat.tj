<?php

namespace App\Http\Middleware;

use App\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /**
     * Map: CMS page slug → translation namespace key
     * (contacts page uses 'contact' namespace in translations)
     */
    private array $pageTranslationMap = [
        'home'      => 'home',
        'about'     => 'about',
        'objects'   => 'objects',
        'investors' => 'investors',
        'services'  => 'services',
        'contacts'  => 'contact',
        'mining'    => 'mining',
        'progress'  => 'progress',
        'career'    => 'career',
    ];

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            ...parent::share($request),
            'locale'    => $locale,
            // Derive asset base from the actual incoming request so images
            // load correctly regardless of whether the site is served via
            // XAMPP (port 80, possibly with a sub-path) or php artisan serve.
            'assetBase' => rtrim(
                $request->getSchemeAndHttpHost() . $request->getBasePath(),
                '/'
            ),
            'flash'     => [
                'success'        => session('success'),
                'error'          => session('error'),
                'warning'        => session('warning'),
                'info'           => session('info'),
                'status'         => session('status'),
                'mining_sent'    => session('mining_sent'),
                'newsletter_sent'=> session('newsletter_sent'),
                'reserved'       => session('reserved'),
                'registered'     => session('registered'),
            ],
            'auth' => [
                'user' => $request->user() ? [
                    'id'       => $request->user()->id,
                    'name'     => $request->user()->name,
                    'email'    => $request->user()->email,
                    'phone'    => $request->user()->phone,
                    'role'     => $request->user()->role,
                    'interest' => $request->user()->interest,
                ] : null,
            ],
            // Контактные данные из config/contacts.php (не env() напрямую —
            // иначе значения пропадают после config:cache в production).
            'contacts' => [
                'phone'    => config('contacts.phone'),
                'whatsapp' => config('contacts.whatsapp'),
                'telegram' => config('contacts.telegram'),
                'email'    => config('contacts.email'),
                'address'  => config('contacts.address'),
            ],
            // Полоса «тестовый режим» (SITE_TEST_BANNER в .env)
            'testBanner' => (bool) config('app.test_banner'),
            // Переводы сайта + CMS-переопределения текущей страницы
            'translations' => $this->loadTranslationsWithCms($locale, $request),
        ];
    }

    /**
     * Загружает базовые переводы из файла и поверх них накладывает
     * активные CMS-секции текущей страницы (если страница известна).
     */
    private function loadTranslationsWithCms(string $locale, Request $request): array
    {
        // Базовые переводы (без кеша — читаем файл напрямую)
        $path = lang_path("{$locale}/site.php");
        $base = file_exists($path) ? require $path : require lang_path('ru/site.php');

        // Определяем страницу по URL
        $cmsPage = $this->detectPage($request);
        if (!$cmsPage) {
            return $base;
        }

        $translationNs = $this->pageTranslationMap[$cmsPage];

        // CMS-переопределения для страницы (без кеша)
        $flat = [];
        PageSection::where('page', $cmsPage)
            ->where('locale', $locale)
            ->where('is_active', true)
            ->get()
            ->each(function ($section) use (&$flat) {
                foreach ($section->content ?? [] as $key => $value) {
                    if (is_string($value) && $value !== '') {
                        $flat[$key] = $value;
                    }
                }
            });
        $overrides = $flat;

        if (!empty($overrides)) {
            $base[$translationNs] = array_merge($base[$translationNs] ?? [], $overrides);
        }

        return $base;
    }

    /**
     * Определяет slug CMS-страницы из текущего URL.
     */
    private function detectPage(Request $request): ?string
    {
        $path = trim($request->path(), '/');
        $map  = [
            ''          => 'home',
            'about'     => 'about',
            'objects'   => 'objects',
            'investors' => 'investors',
            'services'  => 'services',
            'contacts'  => 'contacts',
            'mining'    => 'mining',
            'progress'  => 'progress',
            'career'    => 'career',
        ];
        return $map[$path] ?? null;
    }
}
