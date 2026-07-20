<?php

use App\Http\Controllers\Admin\ApartmentController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DealController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\ConstructionUpdateController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MiningController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PipelineController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ShowcaseProjectController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/objects', [PageController::class, 'objects'])->name('objects');
    // Детальная страница квартиры (SEO-friendly URL: /objects/{проект}/{id})
    Route::get('/objects/{projectSlug}/{apartment}', [PageController::class, 'apartmentShow'])
        ->whereNumber('apartment')
        ->name('apartments.show');
    // 3D-просмотр квартиры
    Route::get('/objects/{projectSlug}/{apartment}/3d', [PageController::class, 'apartment3d'])
        ->whereNumber('apartment')
        ->name('apartments.tour');
    // Детальная страница знакового проекта (блок «Знаковые объекты» на /objects)
    Route::get('/projects/{slug}', [PageController::class, 'projectShow'])->name('projects.show');

    // ═══ Интерактивный выбор квартиры: Генплан → Корпус → Этаж ═══
    Route::get('/complex/{project:slug}', [\App\Http\Controllers\ComplexController::class, 'master'])->name('complex.master');
    Route::get('/complex/{project:slug}/{blockSlug}', [\App\Http\Controllers\ComplexController::class, 'block'])->name('complex.block');
    Route::get('/complex/{project:slug}/{blockSlug}/floor-{number}', [\App\Http\Controllers\ComplexController::class, 'floor'])
        ->whereNumber('number')->name('complex.floor');
    // Страница «Инвесторам» временно скрыта по просьбе заказчика.
    // Роут-имя сохранено (302 → главная), метод investors() и Investors.vue не удалены — для быстрого возврата.
    Route::get('/investors', fn() => redirect('/', 302))->name('investors');
    Route::get('/services', [PageController::class, 'services'])->name('services');
    Route::get('/bms', [PageController::class, 'bms'])->name('bms');
    Route::get('/smart-home', [PageController::class, 'smartHome'])->name('smart-home');
    Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
    Route::get('/news', [PageController::class, 'news'])->name('news');
    Route::get('/news/{slug}', [PageController::class, 'newsShow'])->name('news.show');
    Route::get('/mining', [PageController::class, 'mining'])->name('mining');
    Route::get('/partners', [PageController::class, 'partners'])->name('partners');
    Route::get('/career', [PageController::class, 'career'])->name('career');
    Route::post('/career/apply', [PageController::class, 'careerApply'])->name('career.apply')->middleware('throttle:5,1');
    Route::get('/progress', fn() => redirect('/objects', 301))->name('progress');
    Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
    Route::get('/terms', [PageController::class, 'terms'])->name('terms');

    // Contact & Reserve — с throttle защитой от спама
    Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send')->middleware('throttle:10,1');
    Route::post('/reserve', [PageController::class, 'reserve'])->name('reserve.store')->middleware('throttle:5,1');
    Route::post('/mining/contact', [PageController::class, 'miningContact'])->name('mining.contact')->middleware('throttle:5,1');
    Route::post('/newsletter', [PageController::class, 'newsletter'])->name('newsletter.subscribe')->middleware('throttle:5,1');

    // ═══ КЛИЕНТСКАЯ АВТОРИЗАЦИЯ ═══

    // Регистрация (создаёт реальный аккаунт + Lead в CRM)
    Route::get('/get-started', [PageController::class, 'register'])->name('register');
    Route::post('/get-started', [ClientAuthController::class, 'register'])->name('register.store')->middleware('throttle:5,1');

    // Вход для клиентов
    Route::get('/login', [ClientAuthController::class, 'loginForm'])->name('client.login');
    Route::post('/login', [ClientAuthController::class, 'login'])->name('client.login.post')->middleware('throttle:10,1');

    // Выход
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');

    // Сброс пароля
    Route::get('/forgot-password',         [PasswordResetController::class, 'forgotForm'])->name('password.request')->middleware('guest');
    Route::post('/forgot-password',        [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware(['guest', 'throttle:5,1']);
    Route::get('/reset-password/{token}',  [PasswordResetController::class, 'resetForm'])->name('password.reset')->middleware('guest');
    Route::post('/reset-password',         [PasswordResetController::class, 'reset'])->name('password.update')->middleware(['guest', 'throttle:5,1']);

    // Личный кабинет (только авторизованные)
    Route::middleware('auth')->group(function () {
        Route::get('/cabinet', [ClientAuthController::class, 'cabinet'])->name('client.cabinet');
        Route::put('/cabinet/profile', [ClientAuthController::class, 'updateProfile'])->name('client.profile.update')->middleware('throttle:10,1');
    });

    // Sitemap (XML)
    Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');

    Route::post('/lang/{locale}', function (string $locale) {
        if (in_array($locale, ['ru', 'tj', 'en'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }
        return back();
    })->name('lang.switch');
});

// ═══ ADMIN PANEL ═══
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth (guest only) — throttle: max 5 попыток в минуту
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])
            ->name('login.post')
            ->middleware('throttle:5,1');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 2FA Challenge (после логина, до подтверждения)
    Route::get('/2fa/challenge',  [TwoFactorController::class, 'challenge'])->name('2fa.challenge');
    Route::post('/2fa/verify',    [TwoFactorController::class, 'verify'])->name('2fa.verify')->middleware('throttle:10,1');

    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // 2FA Setup (в профиле администратора)
        Route::get('/2fa/setup',   [TwoFactorController::class, 'setup'])->name('2fa.setup');
        Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('/2fa/disable',[TwoFactorController::class, 'disable'])->name('2fa.disable');

        // Leads (CRM Заявки)
        Route::get('/leads',                        [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/kanban',                 [LeadController::class, 'kanban'])->name('leads.kanban');
        Route::get('/leads/export',                 [LeadController::class, 'export'])->name('leads.export');
        Route::get('/leads/{lead}',                 [LeadController::class, 'show'])->name('leads.show');
        Route::post('/leads',                       [LeadController::class, 'store'])->name('leads.store');
        Route::put('/leads/{lead}',                 [LeadController::class, 'update'])->name('leads.update');
        Route::patch('/leads/{lead}/move-stage',    [LeadController::class, 'moveStage'])->name('leads.move-stage');
        Route::delete('/leads/{lead}',              [LeadController::class, 'destroy'])->name('leads.destroy');

        // Activities (CRM Timeline)
        Route::get('/activities',                   [ActivityController::class, 'index'])->name('activities.index');
        Route::post('/activities',                  [ActivityController::class, 'store'])->name('activities.store');
        Route::put('/activities/{activity}',        [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activities/{activity}',     [ActivityController::class, 'destroy'])->name('activities.destroy');

        // Deals (CRM Сделки)
        Route::get('/deals/kanban',                 [DealController::class, 'kanban'])->name('deals.kanban');
        Route::get('/deals',                        [DealController::class, 'index'])->name('deals.index');
        Route::post('/deals',                       [DealController::class, 'store'])->name('deals.store');
        Route::put('/deals/{deal}',                 [DealController::class, 'update'])->name('deals.update');
        Route::patch('/deals/{deal}/move-stage',    [DealController::class, 'moveStage'])->name('deals.move-stage');
        Route::delete('/deals/{deal}',              [DealController::class, 'destroy'])->name('deals.destroy');

        // Clients (CRM Клиенты)
        Route::get('/clients',            [ClientController::class, 'index'])->name('clients.index');
        Route::post('/clients',           [ClientController::class, 'store'])->name('clients.store');
        Route::put('/clients/{client}',   [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}',[ClientController::class, 'destroy'])->name('clients.destroy');

        // Projects
        Route::get('/projects',              [ProjectController::class, 'index'])->name('projects.index');
        Route::post('/projects',             [ProjectController::class, 'store'])->name('projects.store');
        Route::put('/projects/{project}',    [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Apartments
        Route::get('/apartments',               [ApartmentController::class, 'index'])->name('apartments.index');
        Route::post('/apartments',              [ApartmentController::class, 'store'])->name('apartments.store');
        Route::post('/apartments/bulk',         [ApartmentController::class, 'bulkUpdate'])->name('apartments.bulk');
        Route::post('/apartments/import',       [ApartmentController::class, 'import'])->name('apartments.import');
        Route::put('/apartments/{apartment}',   [ApartmentController::class, 'update'])->name('apartments.update');
        Route::delete('/apartments/{apartment}',[ApartmentController::class, 'destroy'])->name('apartments.destroy');
        // Галерея фото квартиры
        Route::post('/apartments/{apartment}/images',                    [ApartmentController::class, 'storeImages'])->name('apartments.images.store');
        Route::delete('/apartments/{apartment}/images/{image}',          [ApartmentController::class, 'destroyImage'])->name('apartments.images.destroy');
        Route::patch('/apartments/{apartment}/images/{image}/primary',   [ApartmentController::class, 'setPrimaryImage'])->name('apartments.images.primary');
        Route::patch('/apartments/{apartment}/images/reorder',           [ApartmentController::class, 'reorderImages'])->name('apartments.images.reorder');

        // Mining (Горнодобыча)
        Route::get('/mining',                         [MiningController::class, 'index'])->name('mining.index');
        Route::get('/mining/export',                  [MiningController::class, 'export'])->name('mining.export');
        Route::post('/mining',                        [MiningController::class, 'store'])->name('mining.store');
        Route::put('/mining/{miningShipment}',        [MiningController::class, 'update'])->name('mining.update');
        Route::delete('/mining/{miningShipment}',     [MiningController::class, 'destroy'])->name('mining.destroy');

        // Partners (Партнёры)
        Route::get('/partners',              [PartnerController::class, 'index'])->name('partners.index');
        Route::post('/partners',             [PartnerController::class, 'store'])->name('partners.store');
        Route::put('/partners/{partner}',    [PartnerController::class, 'update'])->name('partners.update');
        Route::delete('/partners/{partner}', [PartnerController::class, 'destroy'])->name('partners.destroy');

        // Documents (Документооборот)
        Route::get('/documents',              [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents',             [DocumentController::class, 'store'])->name('documents.store');
        Route::put('/documents/{document}',   [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/documents/{document}',[DocumentController::class, 'destroy'])->name('documents.destroy');

        // ═══ CMS — Контент сайта ═══

        // Site Settings
        Route::get('/settings',  [SiteSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SiteSettingController::class, 'update'])->name('settings.update');

        // Testimonials (Отзывы)
        Route::get('/testimonials',                  [TestimonialController::class, 'index'])->name('testimonials.index');
        Route::post('/testimonials',                 [TestimonialController::class, 'store'])->name('testimonials.store');
        Route::put('/testimonials/{testimonial}',    [TestimonialController::class, 'update'])->name('testimonials.update');
        Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');

        // Gallery
        Route::get('/gallery',                   [GalleryController::class, 'index'])->name('gallery.index');
        Route::post('/gallery',                  [GalleryController::class, 'store'])->name('gallery.store');
        Route::put('/gallery/{galleryImage}',    [GalleryController::class, 'update'])->name('gallery.update');
        Route::delete('/gallery/{galleryImage}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

        // ═══ 3D КВАРТИРЫ ═══
        Route::get('/scene3d',                       [\App\Http\Controllers\Admin\Scene3DController::class, 'index'])->name('scene3d.index');
        Route::patch('/scene3d/{apartment}/toggle',  [\App\Http\Controllers\Admin\Scene3DController::class, 'toggle'])->name('scene3d.toggle');

        // Construction Updates (Прогресс стройки)
        Route::get('/construction',                                 [ConstructionUpdateController::class, 'index'])->name('construction.index');
        Route::post('/construction',                                [ConstructionUpdateController::class, 'store'])->name('construction.store');
        Route::put('/construction/{constructionUpdate}',            [ConstructionUpdateController::class, 'update'])->name('construction.update');
        Route::delete('/construction/{constructionUpdate}',         [ConstructionUpdateController::class, 'destroy'])->name('construction.destroy');

        // News (Новости)
        Route::get('/news',          [NewsController::class, 'index'])->name('news.index');
        Route::post('/news',         [NewsController::class, 'store'])->name('news.store');
        Route::put('/news/{news}',   [NewsController::class, 'update'])->name('news.update');
        Route::delete('/news/{news}',[NewsController::class, 'destroy'])->name('news.destroy');

        // ═══ PIPELINE SETTINGS ═══
        Route::get('/pipelines',                                          [PipelineController::class, 'index'])->name('pipelines.index');
        Route::post('/pipelines',                                         [PipelineController::class, 'store'])->name('pipelines.store');
        Route::put('/pipelines/{pipeline}',                               [PipelineController::class, 'update'])->name('pipelines.update');
        Route::patch('/pipelines/{pipeline}/default',                     [PipelineController::class, 'setDefault'])->name('pipelines.default');
        Route::delete('/pipelines/{pipeline}',                            [PipelineController::class, 'destroy'])->name('pipelines.destroy');
        Route::post('/pipelines/{pipeline}/stages',                       [PipelineController::class, 'storeStage'])->name('pipelines.stages.store');
        Route::put('/pipelines/{pipeline}/stages/{stage}',                [PipelineController::class, 'updateStage'])->name('pipelines.stages.update');
        Route::post('/pipelines/{pipeline}/stages/reorder',               [PipelineController::class, 'reorderStages'])->name('pipelines.stages.reorder');
        Route::delete('/pipelines/{pipeline}/stages/{stage}',             [PipelineController::class, 'destroyStage'])->name('pipelines.stages.destroy');

        // ═══ USERS & ROLES (IAM) ═══
        Route::get('/users',                        [UserController::class, 'index'])->name('users.index');
        Route::post('/users',                       [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',                 [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',              [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        Route::get('/roles',           [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles',          [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}',    [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // ═══ SECURITY CENTER ═══
        Route::get('/security/audit-logs',    [AuditLogController::class, 'index'])->name('security.audit-logs');
        Route::get('/security/login-history', [AuditLogController::class, 'loginHistory'])->name('security.login-history');

        // ═══ Конструктор комплекса (генплан → корпуса → этажи → контуры квартир) ═══
        Route::get('/complex',                                    [\App\Http\Controllers\Admin\ComplexBuilderController::class, 'index'])->name('complex.index');
        Route::post('/complex/{project}/masterplan',              [\App\Http\Controllers\Admin\ComplexBuilderController::class, 'saveMasterplan'])->name('complex.masterplan');
        Route::post('/complex/{project}/blocks',                  [\App\Http\Controllers\Admin\ComplexBuilderController::class, 'storeBlock'])->name('complex.blocks.store');
        Route::post('/complex/blocks/reorder',                    [\App\Http\Controllers\Admin\ComplexBuilderController::class, 'reorderBlocks'])->name('complex.blocks.reorder');
        Route::put('/complex/blocks/{block}',                     [\App\Http\Controllers\Admin\ComplexBuilderController::class, 'updateBlock'])->name('complex.blocks.update');
        Route::delete('/complex/blocks/{block}',                  [\App\Http\Controllers\Admin\ComplexBuilderController::class, 'destroyBlock'])->name('complex.blocks.destroy');
        Route::post('/complex/blocks/{block}/floors/generate',    [\App\Http\Controllers\Admin\ComplexBuilderController::class, 'generateFloors'])->name('complex.floors.generate');
        Route::put('/complex/floors/{floor}',                     [\App\Http\Controllers\Admin\ComplexBuilderController::class, 'updateFloor'])->name('complex.floors.update');
        Route::put('/complex/apartments/{apartment}/shape',       [\App\Http\Controllers\Admin\ComplexBuilderController::class, 'saveApartmentShape'])->name('complex.apartments.shape');

        // ═══ CMS — Знаковые объекты (showcase projects) ═══
        Route::get('/showcase',                              [ShowcaseProjectController::class, 'index'])->name('showcase.index');
        Route::post('/showcase',                             [ShowcaseProjectController::class, 'store'])->name('showcase.store');
        Route::post('/showcase/reorder',                     [ShowcaseProjectController::class, 'reorder'])->name('showcase.reorder');
        Route::put('/showcase/{showcaseProject}',            [ShowcaseProjectController::class, 'update'])->name('showcase.update');
        Route::delete('/showcase/{showcaseProject}',         [ShowcaseProjectController::class, 'destroy'])->name('showcase.destroy');
        Route::post('/showcase/{showcaseProject}/duplicate', [ShowcaseProjectController::class, 'duplicate'])->name('showcase.duplicate');
        Route::patch('/showcase/{showcaseProject}/publish',  [ShowcaseProjectController::class, 'togglePublish'])->name('showcase.publish');
        Route::patch('/showcase/{showcaseProject}/archive',  [ShowcaseProjectController::class, 'toggleArchive'])->name('showcase.archive');

        // ═══ CMS — Page Editor ═══
        Route::get('/cms',                              [CmsController::class, 'index'])->name('cms.index');
        Route::get('/cms/{page}',                       [CmsController::class, 'page'])->name('cms.page');
        Route::post('/cms/{page}/sections',             [CmsController::class, 'upsertSection'])->name('cms.section.upsert');
        Route::post('/cms/{page}/init',                 [CmsController::class, 'initPage'])->name('cms.page.init');
        Route::patch('/cms/{page}/toggle',              [CmsController::class, 'toggleSection'])->name('cms.section.toggle');
        Route::delete('/cms/sections/{section}',        [CmsController::class, 'destroySection'])->name('cms.section.destroy');
    });
});
