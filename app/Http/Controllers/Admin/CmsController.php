<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CmsController extends Controller
{
    // ── Available pages ───────────────────────────────────────────────────
    // 'trans_ns' = ключ в translations (по умолчанию = slug страницы)
    private array $pages = [
        'home'      => ['label' => 'Главная',    'icon' => '🏠', 'trans_ns' => 'home'],
        'objects'   => ['label' => 'Проекты',    'icon' => '🏗️', 'trans_ns' => 'objects'],
        'mining'    => ['label' => 'Уголь',      'icon' => '⛏️', 'trans_ns' => 'mining'],
        'services'  => ['label' => 'Услуги',     'icon' => '🛠️', 'trans_ns' => 'services'],
        'about'     => ['label' => 'О нас',      'icon' => '🏢', 'trans_ns' => 'about'],
        'investors' => ['label' => 'Инвесторам', 'icon' => '📈', 'trans_ns' => 'investors'],
        'contacts'  => ['label' => 'Контакты',   'icon' => '📞', 'trans_ns' => 'contact'],
        'progress'  => ['label' => 'Стройка',    'icon' => '📊', 'trans_ns' => 'progress'],
    ];

    // ── Section schemas: defines fields per page/section ─────────────────
    //
    // ВАЖНО: field 'key' = точная под-ключевая часть translation-ключа.
    // Пример: t('home.hero_geo') → page=home, section=hero, key=hero_geo
    // При сохранении CMS HandleInertiaRequests делает:
    //   translations['home']['hero_geo'] = content['hero_geo']  (override)
    //
    private function getSchemas(): array
    {
        return [

            // ═══════════════════════════════════════════════════════════════
            // HOME — t('home.*')
            // ═══════════════════════════════════════════════════════════════
            'home' => [

                'hero' => [
                    'label' => '🎯 Главный экран (Hero)',
                    'icon'  => '🎯',
                    'fields' => [
                        ['key'=>'hero_title',        'label'=>'Название компании (H1)',     'type'=>'text'],
                        ['key'=>'estd_label',        'label'=>'Подпись под названием',      'type'=>'text'],
                        ['key'=>'hero_geo',          'label'=>'Геобейдж (верхняя строка)', 'type'=>'text'],
                        ['key'=>'hero_tagline_1',    'label'=>'Слоган — строка 1',         'type'=>'text'],
                        ['key'=>'hero_tagline_2',    'label'=>'Слоган — строка 2',         'type'=>'text'],
                        ['key'=>'hero_cta_apts',     'label'=>'Кнопка "Квартиры"',         'type'=>'text'],
                        ['key'=>'hero_cta_apply',    'label'=>'Кнопка "Заявка"',           'type'=>'text'],
                        ['key'=>'hero_more',         'label'=>'Текст ссылки "Подробнее"',  'type'=>'text'],
                    ],
                    'default' => [
                        'hero_title'     => 'QUDRAT',
                        'estd_label'     => 'ООО',
                        'hero_geo'       => 'Душанбе · Таджикистан · с 2010',
                        'hero_tagline_1' => 'Горнодобывающий холдинг и застройщик премиального жилья.',
                        'hero_tagline_2' => 'Два направления — одна философия роста.',
                        'hero_cta_apts'  => 'Смотреть квартиры',
                        'hero_cta_apply' => 'Оставить заявку',
                        'hero_more'      => 'Подробнее',
                    ],
                ],

                'hero_mining' => [
                    'label' => '⛏️ Hero: карточка «Горнодобыча»',
                    'icon'  => '⛏️',
                    'fields' => [
                        ['key'=>'hero_mining_since', 'label'=>'Дата «с ...»',  'type'=>'text'],
                        ['key'=>'hero_mining_title', 'label'=>'Заголовок',      'type'=>'text'],
                        ['key'=>'hero_mining_desc',  'label'=>'Описание',       'type'=>'textarea'],
                    ],
                    'default' => [
                        'hero_mining_since' => 'с 2010',
                        'hero_mining_title' => 'Горнодобыча',
                        'hero_mining_desc'  => 'Добыча угля в Таджикистане. 500K тонн в год, 3 месторождения, промышленный экспорт.',
                    ],
                ],

                'hero_construction' => [
                    'label' => '🏗️ Hero: карточка «Строительство»',
                    'icon'  => '🏗️',
                    'fields' => [
                        ['key'=>'hero_const_since', 'label'=>'Дата «с ...»', 'type'=>'text'],
                        ['key'=>'hero_const_title', 'label'=>'Заголовок',    'type'=>'text'],
                        ['key'=>'hero_const_desc',  'label'=>'Описание',     'type'=>'textarea'],
                    ],
                    'default' => [
                        'hero_const_since' => 'с 2015',
                        'hero_const_title' => 'Строительство',
                        'hero_const_desc'  => 'QUDRAT CITY — 10+ башен, 2000+ квартир в Душанбе. Флагман 2027.',
                    ],
                ],

                'ticker' => [
                    'label' => '📢 Бегущая строка (5 показателей)',
                    'icon'  => '📢',
                    'fields' => [
                        ['key'=>'ticker_towers',   'label'=>'Показатель 1', 'type'=>'text'],
                        ['key'=>'ticker_area',     'label'=>'Показатель 2', 'type'=>'text'],
                        ['key'=>'ticker_apts',     'label'=>'Показатель 3', 'type'=>'text'],
                        ['key'=>'ticker_delivery', 'label'=>'Показатель 4', 'type'=>'text'],
                        ['key'=>'ticker_land',     'label'=>'Показатель 5', 'type'=>'text'],
                    ],
                    'default' => [
                        'ticker_towers'   => '10+ башен',
                        'ticker_area'     => '500 000 м² общей площади',
                        'ticker_apts'     => '2 000+ квартир',
                        'ticker_delivery' => 'Сдача комплекса 2027',
                        'ticker_land'     => '5+ га территории',
                    ],
                ],

                'about_section' => [
                    'label' => '🏢 Секция «О компании»',
                    'icon'  => '🏢',
                    'fields' => [
                        ['key'=>'about_badge',     'label'=>'Бейдж',         'type'=>'text'],
                        ['key'=>'about_founded',   'label'=>'Подпись года',  'type'=>'text'],
                        ['key'=>'about_title',     'label'=>'Заголовок',     'type'=>'text'],
                        ['key'=>'about_title_hl',  'label'=>'Акцент в заголовке',  'type'=>'text'],
                        ['key'=>'about_title_end', 'label'=>'Конец заголовка',      'type'=>'text'],
                        ['key'=>'about_link',      'label'=>'Текст ссылки',  'type'=>'text'],
                    ],
                    'default' => [
                        'about_badge'     => 'О проекте',
                        'about_founded'   => 'Год основания',
                        'about_title'     => 'QUDRAT —',
                        'about_title_hl'  => '15 лет',
                        'about_title_end' => 'роста и доверия',
                        'about_link'      => 'Подробнее о компании',
                    ],
                ],

                'about_timeline' => [
                    'label' => '📅 Секция «О компании» — таймлайн',
                    'icon'  => '📅',
                    'fields' => [
                        ['key'=>'about_tl1_title', 'label'=>'Этап 1: заголовок', 'type'=>'text'],
                        ['key'=>'about_tl1_text',  'label'=>'Этап 1: текст',     'type'=>'textarea'],
                        ['key'=>'about_tl2_title', 'label'=>'Этап 2: заголовок', 'type'=>'text'],
                        ['key'=>'about_tl2_text',  'label'=>'Этап 2: текст',     'type'=>'textarea'],
                        ['key'=>'about_tl3_year',  'label'=>'Этап 3: год',       'type'=>'text'],
                        ['key'=>'about_tl3_title', 'label'=>'Этап 3: заголовок', 'type'=>'text'],
                        ['key'=>'about_tl3_text',  'label'=>'Этап 3: текст',     'type'=>'textarea'],
                    ],
                    'default' => [
                        'about_tl1_title' => 'Основание компании',
                        'about_tl1_text'  => 'Горнодобывающая отрасль — добыча и переработка природных ресурсов на севере страны.',
                        'about_tl2_title' => 'Выход в строительство',
                        'about_tl2_text'  => 'Первые жилые объекты в Душанбе. Строительство стало главным стратегическим направлением.',
                        'about_tl3_year'  => 'Сегодня',
                        'about_tl3_title' => 'QUDRAT CITY',
                        'about_tl3_text'  => 'Флагманский премиум-комплекс в сердце столицы. Тысячи семей уже выбрали QUDRAT.',
                    ],
                ],

                'gallery' => [
                    'label' => '🖼️ Галерея',
                    'icon'  => '🖼️',
                    'fields' => [
                        ['key'=>'gallery_badge', 'label'=>'Бейдж',    'type'=>'text'],
                        ['key'=>'gallery_title', 'label'=>'Заголовок','type'=>'text'],
                        ['key'=>'gallery_hint',  'label'=>'Подсказка','type'=>'text'],
                    ],
                    'default' => [
                        'gallery_badge' => 'Визуализации',
                        'gallery_title' => 'Архитектура нового уровня',
                        'gallery_hint'  => '← Прокрутите →',
                    ],
                ],

                'why' => [
                    'label' => '✅ Секция «Почему QUDRAT»',
                    'icon'  => '✅',
                    'fields' => [
                        ['key'=>'why_badge',    'label'=>'Бейдж',         'type'=>'text'],
                        ['key'=>'why_title_1',  'label'=>'Заголовок — строка 1', 'type'=>'text'],
                        ['key'=>'why_title_2',  'label'=>'Заголовок — строка 2', 'type'=>'text'],
                        ['key'=>'why_title_3',  'label'=>'Заголовок — строка 3', 'type'=>'text'],
                        ['key'=>'why_text',     'label'=>'Описание',       'type'=>'textarea'],
                        ['key'=>'why_link',     'label'=>'Ссылка',         'type'=>'text'],
                    ],
                    'default' => [
                        'why_badge'   => 'Почему QUDRAT',
                        'why_title_1' => 'Разница в деталях,',
                        'why_title_2' => 'которые',
                        'why_title_3' => 'решают всё',
                        'why_text'    => 'Мы не просто строим — мы создаём среду, в которой комфортно жить, работать и инвестировать на десятилетия вперёд.',
                        'why_link'    => 'О компании',
                    ],
                ],

                'why_features' => [
                    'label' => '✅ Секция «Почему QUDRAT» — 4 преимущества',
                    'icon'  => '✅',
                    'fields' => [
                        ['key'=>'why_f1_title', 'label'=>'Преимущество 1: заголовок', 'type'=>'text'],
                        ['key'=>'why_f1_text',  'label'=>'Преимущество 1: текст',     'type'=>'textarea'],
                        ['key'=>'why_f2_title', 'label'=>'Преимущество 2: заголовок', 'type'=>'text'],
                        ['key'=>'why_f2_text',  'label'=>'Преимущество 2: текст',     'type'=>'textarea'],
                        ['key'=>'why_f3_title', 'label'=>'Преимущество 3: заголовок', 'type'=>'text'],
                        ['key'=>'why_f3_text',  'label'=>'Преимущество 3: текст',     'type'=>'textarea'],
                        ['key'=>'why_f4_title', 'label'=>'Преимущество 4: заголовок', 'type'=>'text'],
                        ['key'=>'why_f4_text',  'label'=>'Преимущество 4: текст',     'type'=>'textarea'],
                    ],
                    'default' => [
                        'why_f1_title' => 'Собственный застройщик',
                        'why_f1_text'  => 'Строим сами — без подрядчиков и наценок. Полный контроль качества от фундамента до ключей.',
                        'why_f2_title' => 'Юридическая защита',
                        'why_f2_text'  => 'ДДУ с нотариальным удостоверением, регистрация в ГКН. Ваши права защищены с первого взноса.',
                        'why_f3_title' => 'Рассрочка 0%',
                        'why_f3_text'  => 'Цена фиксируется в день сделки. Рассрочка на весь период строительства без банков и скрытых условий.',
                        'why_f4_title' => 'Управление активом',
                        'why_f4_text'  => 'Собственная УК: охрана, обслуживание, аренда под ключ. Квартира работает на вас без вашего участия.',
                    ],
                ],

                'reviews' => [
                    'label' => '⭐ Отзывы / Статистика доверия',
                    'icon'  => '⭐',
                    'fields' => [
                        ['key'=>'reviews_badge',  'label'=>'Бейдж',           'type'=>'text'],
                        ['key'=>'reviews_title',  'label'=>'Заголовок',        'type'=>'text'],
                        ['key'=>'reviews_hl',     'label'=>'Акцент',           'type'=>'text'],
                        ['key'=>'trust_stat1_v',  'label'=>'Стат 1: значение', 'type'=>'text'],
                        ['key'=>'trust_stat1_l',  'label'=>'Стат 1: подпись',  'type'=>'text'],
                        ['key'=>'trust_stat2_v',  'label'=>'Стат 2: значение', 'type'=>'text'],
                        ['key'=>'trust_stat2_l',  'label'=>'Стат 2: подпись',  'type'=>'text'],
                        ['key'=>'trust_stat3_v',  'label'=>'Стат 3: значение', 'type'=>'text'],
                        ['key'=>'trust_stat3_l',  'label'=>'Стат 3: подпись',  'type'=>'text'],
                        ['key'=>'trust_stat4_v',  'label'=>'Стат 4: значение', 'type'=>'text'],
                        ['key'=>'trust_stat4_l',  'label'=>'Стат 4: подпись',  'type'=>'text'],
                    ],
                    'default' => [
                        'reviews_badge'  => 'Покупатели о нас',
                        'reviews_title'  => 'Нам доверяют',
                        'reviews_hl'     => 'тысячи семей',
                        'trust_stat1_v'  => '1 200+', 'trust_stat1_l' => 'Семей уже выбрали QUDRAT',
                        'trust_stat2_v'  => '98%',    'trust_stat2_l' => 'Объектов сданы в срок',
                        'trust_stat3_v'  => '0',      'trust_stat3_l' => 'Судебных споров с покупателями',
                        'trust_stat4_v'  => '4.9 / 5','trust_stat4_l' => 'Средний рейтинг покупателей',
                    ],
                ],

                'invest_block' => [
                    'label' => '💰 Секция «Инвесторам»',
                    'icon'  => '💰',
                    'fields' => [
                        ['key'=>'invest_badge',   'label'=>'Бейдж',         'type'=>'text'],
                        ['key'=>'invest_title',   'label'=>'Заголовок',     'type'=>'text'],
                        ['key'=>'invest_title_hl','label'=>'Акцент',        'type'=>'text'],
                        ['key'=>'invest_text',    'label'=>'Описание',      'type'=>'textarea'],
                        ['key'=>'invest_cta',     'label'=>'Кнопка CTA',   'type'=>'text'],
                        ['key'=>'invest_s1_v',    'label'=>'Стат 1: значение','type'=>'text'],
                        ['key'=>'invest_s1_l',    'label'=>'Стат 1: подпись', 'type'=>'text'],
                        ['key'=>'invest_s2_v',    'label'=>'Стат 2: значение','type'=>'text'],
                        ['key'=>'invest_s2_l',    'label'=>'Стат 2: подпись', 'type'=>'text'],
                        ['key'=>'invest_s3_v',    'label'=>'Стат 3: значение','type'=>'text'],
                        ['key'=>'invest_s3_l',    'label'=>'Стат 3: подпись', 'type'=>'text'],
                        ['key'=>'invest_s4_v',    'label'=>'Стат 4: значение','type'=>'text'],
                        ['key'=>'invest_s4_l',    'label'=>'Стат 4: подпись', 'type'=>'text'],
                    ],
                    'default' => [
                        'invest_badge'    => 'Инвесторам',
                        'invest_title'    => 'Недвижимость, которая',
                        'invest_title_hl' => 'приносит доход',
                        'invest_text'     => 'Мы создаём активы с реальной доходностью. Прозрачная структура, юридическая защита, индивидуальные условия для каждого инвестора.',
                        'invest_cta'      => 'Стать инвестором',
                        'invest_s1_v'     => 'от 12%', 'invest_s1_l' => 'Среднегодовая доходность',
                        'invest_s2_v'     => '15',     'invest_s2_l' => 'Лет успешной работы',
                        'invest_s3_v'     => '1000+',  'invest_s3_l' => 'Довольных инвесторов',
                        'invest_s4_v'     => '25+',    'invest_s4_l' => 'Наград и премий отрасли',
                    ],
                ],

                'contact_section' => [
                    'label' => '📞 Контактная секция',
                    'icon'  => '📞',
                    'fields' => [
                        ['key'=>'contact_badge',  'label'=>'Бейдж',         'type'=>'text'],
                        ['key'=>'contact_title',  'label'=>'Заголовок',     'type'=>'text'],
                        ['key'=>'contact_sub',    'label'=>'Подзаголовок',  'type'=>'textarea'],
                        ['key'=>'contact_btn',    'label'=>'Кнопка',        'type'=>'text'],
                        ['key'=>'contact_hours',  'label'=>'Часы работы',   'type'=>'text'],
                        ['key'=>'contact_office', 'label'=>'Адрес офиса',   'type'=>'text'],
                        ['key'=>'contact_city',   'label'=>'Город',         'type'=>'text'],
                    ],
                    'default' => [
                        'contact_badge'  => 'Связаться с нами',
                        'contact_title'  => 'Оставьте заявку',
                        'contact_sub'    => 'Перезвоним в течение 30 минут и ответим на все вопросы.',
                        'contact_btn'    => 'Отправить заявку',
                        'contact_hours'  => 'Пн – Вс · 9:00 – 20:00',
                        'contact_office' => 'QUDRAT CITY, офис продаж',
                        'contact_city'   => 'г. Душанбе',
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════════════
            // ABOUT — t('about.*')
            // ═══════════════════════════════════════════════════════════════
            'about' => [

                'page' => [
                    'label' => '🎯 Заголовок страницы',
                    'icon'  => '🎯',
                    'fields' => [
                        ['key'=>'page_badge',  'label'=>'Бейдж',       'type'=>'text'],
                        ['key'=>'page_title',  'label'=>'Заголовок H1','type'=>'text'],
                    ],
                    'default' => [
                        'page_badge'  => 'О компании',
                        'page_title'  => 'Строим будущее с 2010 года',
                    ],
                ],

                'story' => [
                    'label' => '📖 История компании',
                    'icon'  => '📖',
                    'fields' => [
                        ['key'=>'story_badge',  'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'story_title',  'label'=>'Заголовок',    'type'=>'text'],
                        ['key'=>'story_text1',  'label'=>'Параграф 1',   'type'=>'textarea'],
                        ['key'=>'story_text2',  'label'=>'Параграф 2',   'type'=>'textarea'],
                        ['key'=>'story_text3',  'label'=>'Параграф 3',   'type'=>'textarea'],
                        ['key'=>'years_badge',  'label'=>'Бейдж лет',    'type'=>'text'],
                        ['key'=>'years_label',  'label'=>'Подпись лет',  'type'=>'text'],
                    ],
                    'default' => [
                        'story_badge'  => 'История',
                        'story_title'  => 'От горнодобывающей к строительству',
                        'story_text1'  => 'QUDRAT основана в 2010 году как горнодобывающая компания, работающая с природными ресурсами Таджикистана.',
                        'story_text2'  => 'Пройдя путь от добычи ресурсов до создания жилых комплексов, мы принесли в строительство тот же принцип: глубокое понимание материала и инженерная точность.',
                        'story_text3'  => 'Флагманский проект — умный жилой комплекс в Душанбе, оснащённый системами «умного дома», закрытой территорией и инфраструктурой международного уровня.',
                        'years_badge'  => '15+',
                        'years_label'  => 'лет опыта',
                    ],
                ],

                'values' => [
                    'label' => '🏆 Ценности (4 карточки)',
                    'icon'  => '🏆',
                    'fields' => [
                        ['key'=>'values_badge', 'label'=>'Бейдж',    'type'=>'text'],
                        ['key'=>'values_title', 'label'=>'Заголовок','type'=>'text'],
                        ['key'=>'val1_title',   'label'=>'Ценность 1: заголовок','type'=>'text'],
                        ['key'=>'val1_text',    'label'=>'Ценность 1: текст',    'type'=>'textarea'],
                        ['key'=>'val2_title',   'label'=>'Ценность 2: заголовок','type'=>'text'],
                        ['key'=>'val2_text',    'label'=>'Ценность 2: текст',    'type'=>'textarea'],
                        ['key'=>'val3_title',   'label'=>'Ценность 3: заголовок','type'=>'text'],
                        ['key'=>'val3_text',    'label'=>'Ценность 3: текст',    'type'=>'textarea'],
                        ['key'=>'val4_title',   'label'=>'Ценность 4: заголовок','type'=>'text'],
                        ['key'=>'val4_text',    'label'=>'Ценность 4: текст',    'type'=>'textarea'],
                    ],
                    'default' => [
                        'values_badge' => 'Наши принципы',
                        'values_title' => 'Ценности компании',
                        'val1_title'   => 'Качество',
                        'val1_text'    => 'Используем только сертифицированные материалы и проверенные технологии строительства.',
                        'val2_title'   => 'Надёжность',
                        'val2_text'    => 'За 15 лет не было ни одного незавершённого проекта. Каждый объект сдаём в срок.',
                        'val3_title'   => 'Инновации',
                        'val3_text'    => 'Внедряем системы умного дома, IoT-решения и современные инженерные системы.',
                        'val4_title'   => 'Прозрачность',
                        'val4_text'    => 'Открытая отчётность для инвесторов и регулярные обновления о ходе строительства.',
                    ],
                ],

                'timeline' => [
                    'label' => '📅 Таймлайн истории',
                    'icon'  => '📅',
                    'fields' => [
                        ['key'=>'timeline_badge', 'label'=>'Бейдж',    'type'=>'text'],
                        ['key'=>'timeline_title', 'label'=>'Заголовок','type'=>'text'],
                        ['key'=>'tl1_year',       'label'=>'Этап 1: год',       'type'=>'text'],
                        ['key'=>'tl1_title',      'label'=>'Этап 1: заголовок', 'type'=>'text'],
                        ['key'=>'tl1_text',       'label'=>'Этап 1: описание',  'type'=>'textarea'],
                        ['key'=>'tl2_year',       'label'=>'Этап 2: год',       'type'=>'text'],
                        ['key'=>'tl2_title',      'label'=>'Этап 2: заголовок', 'type'=>'text'],
                        ['key'=>'tl2_text',       'label'=>'Этап 2: описание',  'type'=>'textarea'],
                        ['key'=>'tl3_year',       'label'=>'Этап 3: год',       'type'=>'text'],
                        ['key'=>'tl3_title',      'label'=>'Этап 3: заголовок', 'type'=>'text'],
                        ['key'=>'tl3_text',       'label'=>'Этап 3: описание',  'type'=>'textarea'],
                        ['key'=>'tl4_year',       'label'=>'Этап 4: год',       'type'=>'text'],
                        ['key'=>'tl4_title',      'label'=>'Этап 4: заголовок', 'type'=>'text'],
                        ['key'=>'tl4_text',       'label'=>'Этап 4: описание',  'type'=>'textarea'],
                        ['key'=>'tl5_year',       'label'=>'Этап 5: год',       'type'=>'text'],
                        ['key'=>'tl5_title',      'label'=>'Этап 5: заголовок', 'type'=>'text'],
                        ['key'=>'tl5_text',       'label'=>'Этап 5: описание',  'type'=>'textarea'],
                    ],
                    'default' => [
                        'timeline_badge' => 'Путь компании',
                        'timeline_title' => 'История QUDRAT',
                        'tl1_year'  => '2010', 'tl1_title' => 'Основание компании',
                        'tl1_text'  => 'QUDRAT зарегистрирована как горнодобывающая компания.',
                        'tl2_year'  => '2014', 'tl2_title' => 'Диверсификация',
                        'tl2_text'  => 'Расширение деятельности. Компания начинает изучать рынок недвижимости.',
                        'tl3_year'  => '2018', 'tl3_title' => 'Первые строительные проекты',
                        'tl3_text'  => 'QUDRAT приступает к реализации первых строительных объектов.',
                        'tl4_year'  => '2021', 'tl4_title' => 'Девелопмент',
                        'tl4_text'  => 'Стратегический сдвиг в сторону жилого девелопмента.',
                        'tl5_year'  => '2024', 'tl5_title' => 'Умный жилой комплекс',
                        'tl5_text'  => 'Старт флагманского проекта — ЖК QUDRAT с технологиями умного дома.',
                    ],
                ],

                'awards' => [
                    'label' => '🏆 Награды и достижения (6 наград)',
                    'icon'  => '🏆',
                    'fields' => [
                        ['key'=>'awards_badge',  'label'=>'Бейдж секции',    'type'=>'text'],
                        ['key'=>'awards_title',  'label'=>'Заголовок',       'type'=>'text'],
                        ['key'=>'awards_sub',    'label'=>'Подзаголовок',    'type'=>'textarea'],
                        ['key'=>'aw1_year',  'label'=>'Награда 1: год',         'type'=>'text'],
                        ['key'=>'aw1_title', 'label'=>'Награда 1: название',    'type'=>'text'],
                        ['key'=>'aw1_org',   'label'=>'Награда 1: организация', 'type'=>'text'],
                        ['key'=>'aw2_year',  'label'=>'Награда 2: год',         'type'=>'text'],
                        ['key'=>'aw2_title', 'label'=>'Награда 2: название',    'type'=>'text'],
                        ['key'=>'aw2_org',   'label'=>'Награда 2: организация', 'type'=>'text'],
                        ['key'=>'aw3_year',  'label'=>'Награда 3: год',         'type'=>'text'],
                        ['key'=>'aw3_title', 'label'=>'Награда 3: название',    'type'=>'text'],
                        ['key'=>'aw3_org',   'label'=>'Награда 3: организация', 'type'=>'text'],
                        ['key'=>'aw4_year',  'label'=>'Награда 4: год',         'type'=>'text'],
                        ['key'=>'aw4_title', 'label'=>'Награда 4: название',    'type'=>'text'],
                        ['key'=>'aw4_org',   'label'=>'Награда 4: организация', 'type'=>'text'],
                        ['key'=>'aw5_year',  'label'=>'Награда 5: год',         'type'=>'text'],
                        ['key'=>'aw5_title', 'label'=>'Награда 5: название',    'type'=>'text'],
                        ['key'=>'aw5_org',   'label'=>'Награда 5: организация', 'type'=>'text'],
                        ['key'=>'aw6_year',  'label'=>'Награда 6: год',         'type'=>'text'],
                        ['key'=>'aw6_title', 'label'=>'Награда 6: название',    'type'=>'text'],
                        ['key'=>'aw6_org',   'label'=>'Награда 6: организация', 'type'=>'text'],
                    ],
                    'default' => [
                        'awards_badge'  => 'Признание',
                        'awards_title'  => 'Награды и достижения',
                        'awards_sub'    => 'Международное признание нашей работы подтверждает приверженность качеству и инновациям',
                        'aw1_year'  => '2024', 'aw1_title' => 'Лучший девелопер года',            'aw1_org' => 'Tajikistan Real Estate Awards',
                        'aw2_year'  => '2023', 'aw2_title' => 'Инновации в строительстве',        'aw2_org' => 'Central Asia Construction Forum',
                        'aw3_year'  => '2022', 'aw3_title' => 'Лучший умный ЖК',                  'aw3_org' => 'Smart City Expo Dushanbe',
                        'aw4_year'  => '2021', 'aw4_title' => 'Ответственная горнодобыча',        'aw4_org' => 'International Mining Standards',
                        'aw5_year'  => '2020', 'aw5_title' => 'Инвестиционная привлекательность', 'aw5_org' => 'Tajikistan Business Award',
                        'aw6_year'  => '2019', 'aw6_title' => 'Лучший работодатель',              'aw6_org' => 'HR Excellence Awards TJ',
                    ],
                ],

                'team' => [
                    'label' => '👥 Команда',
                    'icon'  => '👥',
                    'fields' => [
                        ['key'=>'team_badge',  'label'=>'Бейдж',    'type'=>'text'],
                        ['key'=>'team_title',  'label'=>'Заголовок','type'=>'text'],
                        ['key'=>'team_text',   'label'=>'Текст',    'type'=>'textarea'],
                        ['key'=>'ts1_v', 'label'=>'Стат 1: значение','type'=>'text'],
                        ['key'=>'ts1_l', 'label'=>'Стат 1: подпись', 'type'=>'text'],
                        ['key'=>'ts2_v', 'label'=>'Стат 2: значение','type'=>'text'],
                        ['key'=>'ts2_l', 'label'=>'Стат 2: подпись', 'type'=>'text'],
                        ['key'=>'ts3_v', 'label'=>'Стат 3: значение','type'=>'text'],
                        ['key'=>'ts3_l', 'label'=>'Стат 3: подпись', 'type'=>'text'],
                        ['key'=>'ts4_v', 'label'=>'Стат 4: значение','type'=>'text'],
                        ['key'=>'ts4_l', 'label'=>'Стат 4: подпись', 'type'=>'text'],
                    ],
                    'default' => [
                        'team_badge'  => 'Команда',
                        'team_title'  => 'Профессионалы своего дела',
                        'team_text'   => 'В команде QUDRAT — инженеры, архитекторы и менеджеры с международным опытом.',
                        'ts1_v' => '80+', 'ts1_l' => 'специалистов',
                        'ts2_v' => '15',  'ts2_l' => 'лет на рынке',
                        'ts3_v' => '12',  'ts3_l' => 'завершённых объектов',
                        'ts4_v' => '3',   'ts4_l' => 'направления',
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════════════
            // CONTACTS — t('contact.*')
            // ═══════════════════════════════════════════════════════════════
            'contacts' => [

                'main' => [
                    'label' => '📋 Основные тексты страницы',
                    'icon'  => '📋',
                    'fields' => [
                        ['key'=>'title',         'label'=>'Заголовок страницы',    'type'=>'text'],
                        ['key'=>'page_subtitle', 'label'=>'Подзаголовок страницы', 'type'=>'text'],
                        ['key'=>'form_title',    'label'=>'Заголовок формы',       'type'=>'text'],
                        ['key'=>'form_subtitle', 'label'=>'Подзаголовок формы',    'type'=>'text'],
                        ['key'=>'hours_title',   'label'=>'Подпись "Часы работы"', 'type'=>'text'],
                        ['key'=>'map_note',      'label'=>'Примечание к карте',    'type'=>'text'],
                        ['key'=>'map_city',      'label'=>'Город (карта)',         'type'=>'text'],
                    ],
                    'default' => [
                        'title'         => 'Свяжитесь с нами',
                        'page_subtitle' => 'Мы ответим в течение одного рабочего дня',
                        'form_title'    => 'Оставить заявку',
                        'form_subtitle' => 'Заполните форму и мы свяжемся с вами в ближайшее время',
                        'hours_title'   => 'Время работы',
                        'map_note'      => 'Точный адрес предоставляется при обращении',
                        'map_city'      => 'Душанбе, Таджикистан',
                    ],
                ],

                'hours' => [
                    'label' => '🕐 Часы работы',
                    'icon'  => '🕐',
                    'fields' => [
                        ['key'=>'h1_days',   'label'=>'Строка 1: дни',   'type'=>'text'],
                        ['key'=>'h1_time',   'label'=>'Строка 1: время', 'type'=>'text'],
                        ['key'=>'h2_days',   'label'=>'Строка 2: дни',   'type'=>'text'],
                        ['key'=>'h2_time',   'label'=>'Строка 2: время', 'type'=>'text'],
                        ['key'=>'h3_days',   'label'=>'Строка 3: дни',   'type'=>'text'],
                        ['key'=>'h3_closed', 'label'=>'Строка 3: статус','type'=>'text'],
                    ],
                    'default' => [
                        'h1_days'   => 'Понедельник — Пятница',
                        'h1_time'   => '9:00 — 18:00',
                        'h2_days'   => 'Суббота',
                        'h2_time'   => '10:00 — 15:00',
                        'h3_days'   => 'Воскресенье',
                        'h3_closed' => 'Выходной',
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════════════
            // OBJECTS — t('objects.*')
            // ═══════════════════════════════════════════════════════════════
            'objects' => [

                'hero' => [
                    'label' => '🎯 Заголовок страницы',
                    'icon'  => '🎯',
                    'fields' => [
                        ['key'=>'hero_title',    'label'=>'Заголовок',    'type'=>'text'],
                        ['key'=>'hero_subtitle', 'label'=>'Подзаголовок', 'type'=>'textarea'],
                    ],
                    'default' => [
                        'hero_title'    => 'Выберите свою квартиру',
                        'hero_subtitle' => 'Современные планировки, панорамное остекление, умные системы безопасности. Ввод в эксплуатацию — 2026 год.',
                    ],
                ],

                'cta' => [
                    'label' => '📣 Блок CTA (низ страницы)',
                    'icon'  => '📣',
                    'fields' => [
                        ['key'=>'cta_title', 'label'=>'Заголовок CTA', 'type'=>'text'],
                        ['key'=>'cta_desc',  'label'=>'Описание',      'type'=>'textarea'],
                        ['key'=>'cta_btn',   'label'=>'Кнопка',        'type'=>'text'],
                    ],
                    'default' => [
                        'cta_title' => 'Не нашли нужный вариант?',
                        'cta_desc'  => 'Оставьте заявку — подберём квартиру по вашим параметрам',
                        'cta_btn'   => 'Оставить заявку',
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════════════
            // MINING — t('mining.*')
            // ═══════════════════════════════════════════════════════════════
            'mining' => [

                'hero' => [
                    'label' => '🎯 Заголовок страницы',
                    'icon'  => '🎯',
                    'fields' => [
                        ['key'=>'badge',    'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'title',    'label'=>'Заголовок H1', 'type'=>'text'],
                        ['key'=>'subtitle', 'label'=>'Подзаголовок', 'type'=>'textarea'],
                    ],
                    'default' => [
                        'badge'    => 'Горнодобывающий бизнес',
                        'title'    => 'Уголь из Таджикистана',
                        'subtitle' => 'Добыча и поставки энергетического и коксующегося угля на международные рынки',
                    ],
                ],

                'about' => [
                    'label' => '⛏️ Секция «О направлении»',
                    'icon'  => '⛏️',
                    'fields' => [
                        ['key'=>'about_badge',    'label'=>'Бейдж',           'type'=>'text'],
                        ['key'=>'about_title',    'label'=>'Заголовок',        'type'=>'text'],
                        ['key'=>'about_title_hl', 'label'=>'Акцент заголовка', 'type'=>'text'],
                    ],
                    'default' => [
                        'about_badge'    => 'О направлении',
                        'about_title'    => 'Уголь — основа',
                        'about_title_hl' => 'энергетики страны',
                    ],
                ],

                'deposit' => [
                    'label' => '🗺️ Месторождение «Гузн»',
                    'icon'  => '🗺️',
                    'fields' => [
                        ['key'=>'deposit_badge',  'label'=>'Бейдж',         'type'=>'text'],
                        ['key'=>'deposit_name',   'label'=>'Название',      'type'=>'text'],
                        ['key'=>'license_badge',  'label'=>'Бейдж лицензии','type'=>'text'],
                        ['key'=>'license_serial', 'label'=>'Серийный номер','type'=>'text'],
                        ['key'=>'license_date',   'label'=>'Дата выдачи',   'type'=>'text'],
                        ['key'=>'location_title', 'label'=>'Заголовок местоположения','type'=>'text'],
                        ['key'=>'location_sub',   'label'=>'Регион',        'type'=>'text'],
                        ['key'=>'geo_note',       'label'=>'Географическая заметка',  'type'=>'text'],
                    ],
                    'default' => [
                        'deposit_badge'  => 'Месторождение',
                        'deposit_name'   => 'Месторождение «Гузн»',
                        'license_badge'  => 'Лицензия Правительства РТ',
                        'license_serial' => 'Серия МПИТ №0000031',
                        'license_date'   => 'Выдана 27 мая 2017 года · право пользования недрами · месторождение «Гузн»',
                        'location_title' => 'Месторасположение',
                        'location_sub'   => 'Северный Таджикистан',
                        'geo_note'       => 'Месторождение Гузн · Согдийская область · Таджикистан',
                    ],
                ],

                'export' => [
                    'label' => '🌍 Экспортные направления',
                    'icon'  => '🌍',
                    'fields' => [
                        ['key'=>'export_badge',    'label'=>'Бейдж',            'type'=>'text'],
                        ['key'=>'export_title',    'label'=>'Заголовок',         'type'=>'text'],
                        ['key'=>'logistic_badge',  'label'=>'Бейдж логистики',   'type'=>'text'],
                        ['key'=>'logistic_title',  'label'=>'Заголовок логистики','type'=>'text'],
                    ],
                    'default' => [
                        'export_badge'   => 'Рынки сбыта',
                        'export_title'   => 'Экспортные направления',
                        'logistic_badge' => 'Логистика',
                        'logistic_title' => 'Маршруты доставки',
                    ],
                ],

                'offers' => [
                    'label' => '📦 Продукция и предложения',
                    'icon'  => '📦',
                    'fields' => [
                        ['key'=>'offers_badge',  'label'=>'Бейдж',         'type'=>'text'],
                        ['key'=>'offers_title',  'label'=>'Заголовок',     'type'=>'text'],
                        ['key'=>'process_badge', 'label'=>'Бейдж процесса','type'=>'text'],
                        ['key'=>'process_title', 'label'=>'Загол. процесса','type'=>'text'],
                    ],
                    'default' => [
                        'offers_badge'  => 'Продукция и услуги',
                        'offers_title'  => 'Что мы предлагаем',
                        'process_badge' => 'Цикл производства',
                        'process_title' => 'Как мы работаем',
                    ],
                ],

                'b2b' => [
                    'label' => '🤝 B2B / Контактная форма',
                    'icon'  => '🤝',
                    'fields' => [
                        ['key'=>'b2b_badge',    'label'=>'Бейдж B2B',     'type'=>'text'],
                        ['key'=>'b2b_title',    'label'=>'Заголовок B2B', 'type'=>'text'],
                        ['key'=>'collab_badge', 'label'=>'Бейдж формы',  'type'=>'text'],
                        ['key'=>'collab_title', 'label'=>'Заголовок формы','type'=>'text'],
                        ['key'=>'prod_badge',   'label'=>'Бейдж производства','type'=>'text'],
                        ['key'=>'prod_title',   'label'=>'Заголовок производства','type'=>'text'],
                    ],
                    'default' => [
                        'b2b_badge'    => 'Для партнёров · B2B',
                        'b2b_title'    => 'Технические характеристики угольной продукции',
                        'collab_badge' => 'Сотрудничество',
                        'collab_title' => 'Заявка на поставку угля',
                        'prod_badge'   => 'Производство',
                        'prod_title'   => 'Добыча в реальных условиях',
                    ],
                ],

                'docs' => [
                    'label' => '📄 Документы / Лицензии',
                    'icon'  => '📄',
                    'fields' => [
                        ['key'=>'docs_badge', 'label'=>'Бейдж',     'type'=>'text'],
                        ['key'=>'docs_hint',  'label'=>'Подсказка', 'type'=>'text'],
                    ],
                    'default' => [
                        'docs_badge' => 'Лицензии и документы',
                        'docs_hint'  => 'Нажмите на документ для увеличения · Оригиналы предоставляются по запросу',
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════════════
            // INVESTORS — t('investors.*')
            // ═══════════════════════════════════════════════════════════════
            'investors' => [

                'hero' => [
                    'label' => '🎯 Заголовок страницы',
                    'icon'  => '🎯',
                    'fields' => [
                        ['key'=>'badge',    'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'title',    'label'=>'Заголовок H1', 'type'=>'text'],
                        ['key'=>'subtitle', 'label'=>'Подзаголовок', 'type'=>'textarea'],
                        ['key'=>'cta_btn',  'label'=>'Кнопка CTA',  'type'=>'text'],
                    ],
                    'default' => [
                        'badge'    => 'Инвесторам',
                        'title'    => 'Ваши инвестиции работают',
                        'subtitle' => 'Вложите капитал в недвижимость Таджикистана с гарантированной доходностью',
                        'cta_btn'  => 'Связаться с нами',
                    ],
                ],

                'stats' => [
                    'label' => '📊 Ключевые показатели (4 цифры)',
                    'icon'  => '📊',
                    'fields' => [
                        ['key'=>'stat1_v', 'label'=>'Стат 1: значение', 'type'=>'text'],
                        ['key'=>'stat1_l', 'label'=>'Стат 1: подпись',  'type'=>'text'],
                        ['key'=>'stat2_v', 'label'=>'Стат 2: значение', 'type'=>'text'],
                        ['key'=>'stat2_l', 'label'=>'Стат 2: подпись',  'type'=>'text'],
                        ['key'=>'stat3_v', 'label'=>'Стат 3: значение', 'type'=>'text'],
                        ['key'=>'stat3_l', 'label'=>'Стат 3: подпись',  'type'=>'text'],
                        ['key'=>'stat4_v', 'label'=>'Стат 4: значение', 'type'=>'text'],
                        ['key'=>'stat4_l', 'label'=>'Стат 4: подпись',  'type'=>'text'],
                    ],
                    'default' => [
                        'stat1_v' => '$2M+', 'stat1_l' => 'Привлечено инвестиций',
                        'stat2_v' => '18%',  'stat2_l' => 'Средняя доходность',
                        'stat3_v' => '100%', 'stat3_l' => 'Выполнение обязательств',
                        'stat4_v' => '15+',  'stat4_l' => 'Лет на рынке',
                    ],
                ],

                'option_1' => [
                    'label' => '💼 Вариант 1: Покупка квартиры',
                    'icon'  => '💼',
                    'fields' => [
                        ['key'=>'opt1_title',  'label'=>'Название',    'type'=>'text'],
                        ['key'=>'opt1_return', 'label'=>'Доходность',  'type'=>'text'],
                        ['key'=>'opt1_desc',   'label'=>'Описание',    'type'=>'textarea'],
                        ['key'=>'opt1_f1',     'label'=>'Фича 1',      'type'=>'text'],
                        ['key'=>'opt1_f2',     'label'=>'Фича 2',      'type'=>'text'],
                        ['key'=>'opt1_f3',     'label'=>'Фича 3',      'type'=>'text'],
                        ['key'=>'opt1_f4',     'label'=>'Фича 4',      'type'=>'text'],
                    ],
                    'default' => [
                        'opt1_title'  => 'Покупка квартиры',
                        'opt1_return' => 'от 12% годовых',
                        'opt1_desc'   => 'Приобретение квартиры на этапе строительства с целью перепродажи или аренды',
                        'opt1_f1'     => 'Рост стоимости 12–20% в год',
                        'opt1_f2'     => 'Возможность рассрочки',
                        'opt1_f3'     => 'Полное юридическое сопровождение',
                        'opt1_f4'     => 'Управление арендой',
                    ],
                ],

                'option_2' => [
                    'label' => '💼 Вариант 2: Долевое участие',
                    'icon'  => '💼',
                    'fields' => [
                        ['key'=>'opt2_title',  'label'=>'Название',   'type'=>'text'],
                        ['key'=>'opt2_return', 'label'=>'Доходность', 'type'=>'text'],
                        ['key'=>'opt2_desc',   'label'=>'Описание',   'type'=>'textarea'],
                        ['key'=>'opt2_f1',     'label'=>'Фича 1',     'type'=>'text'],
                        ['key'=>'opt2_f2',     'label'=>'Фича 2',     'type'=>'text'],
                        ['key'=>'opt2_f3',     'label'=>'Фича 3',     'type'=>'text'],
                        ['key'=>'opt2_f4',     'label'=>'Фича 4',     'type'=>'text'],
                    ],
                    'default' => [
                        'opt2_title'  => 'Долевое участие',
                        'opt2_return' => 'от 18% годовых',
                        'opt2_desc'   => 'Участие в проекте как дольщик с фиксированной доходностью и чёткими сроками',
                        'opt2_f1'     => 'Фиксированная доходность 18%',
                        'opt2_f2'     => 'Ежеквартальные выплаты',
                        'opt2_f3'     => 'Участие в прибыли проекта',
                        'opt2_f4'     => 'Персональный менеджер',
                    ],
                ],

                'option_3' => [
                    'label' => '💼 Вариант 3: Коммерческие площади',
                    'icon'  => '💼',
                    'fields' => [
                        ['key'=>'opt3_title',  'label'=>'Название',   'type'=>'text'],
                        ['key'=>'opt3_return', 'label'=>'Доходность', 'type'=>'text'],
                        ['key'=>'opt3_desc',   'label'=>'Описание',   'type'=>'textarea'],
                        ['key'=>'opt3_f1',     'label'=>'Фича 1',     'type'=>'text'],
                        ['key'=>'opt3_f2',     'label'=>'Фича 2',     'type'=>'text'],
                        ['key'=>'opt3_f3',     'label'=>'Фича 3',     'type'=>'text'],
                        ['key'=>'opt3_f4',     'label'=>'Фича 4',     'type'=>'text'],
                    ],
                    'default' => [
                        'opt3_title'  => 'Коммерческие площади',
                        'opt3_return' => 'от 15% годовых',
                        'opt3_desc'   => 'Инвестиции в коммерческую недвижимость на первых этажах комплекса',
                        'opt3_f1'     => 'Высокий трафик жильцов',
                        'opt3_f2'     => 'Долгосрочные арендаторы',
                        'opt3_f3'     => 'Гарантия аренды 2 года',
                        'opt3_f4'     => 'Управляющая компания',
                    ],
                ],

                'reasons' => [
                    'label' => '✅ Почему выбирают нас (4 причины)',
                    'icon'  => '✅',
                    'fields' => [
                        ['key'=>'reason1_title', 'label'=>'Причина 1: заголовок', 'type'=>'text'],
                        ['key'=>'reason1_text',  'label'=>'Причина 1: текст',     'type'=>'textarea'],
                        ['key'=>'reason2_title', 'label'=>'Причина 2: заголовок', 'type'=>'text'],
                        ['key'=>'reason2_text',  'label'=>'Причина 2: текст',     'type'=>'textarea'],
                        ['key'=>'reason3_title', 'label'=>'Причина 3: заголовок', 'type'=>'text'],
                        ['key'=>'reason3_text',  'label'=>'Причина 3: текст',     'type'=>'textarea'],
                        ['key'=>'reason4_title', 'label'=>'Причина 4: заголовок', 'type'=>'text'],
                        ['key'=>'reason4_text',  'label'=>'Причина 4: текст',     'type'=>'textarea'],
                    ],
                    'default' => [
                        'reason1_title' => 'Юридическая защита',
                        'reason1_text'  => 'Все сделки оформляются официально. Нотариально заверенные договоры с государственной регистрацией.',
                        'reason2_title' => 'Прозрачная отчётность',
                        'reason2_text'  => 'Ежеквартальные отчёты о ходе строительства и финансовом состоянии проекта.',
                        'reason3_title' => 'Опыт 15 лет',
                        'reason3_text'  => 'Ни одного незавершённого проекта за 15 лет. Каждый инвестор получил свои средства в срок.',
                        'reason4_title' => 'Растущий рынок',
                        'reason4_text'  => 'Рынок недвижимости Таджикистана растёт на 8–12% в год. QUDRAT опережает рыночный рост.',
                    ],
                ],

                'calc' => [
                    'label' => '🧮 Калькулятор доходности',
                    'icon'  => '🧮',
                    'fields' => [
                        ['key'=>'calc_title', 'label'=>'Заголовок калькулятора', 'type'=>'text'],
                        ['key'=>'amount',     'label'=>'Подпись "Сумма"',        'type'=>'text'],
                        ['key'=>'period',     'label'=>'Подпись "Период"',       'type'=>'text'],
                        ['key'=>'income',     'label'=>'Подпись "Доход"',        'type'=>'text'],
                        ['key'=>'total',      'label'=>'Подпись "Итого"',        'type'=>'text'],
                        ['key'=>'monthly',    'label'=>'Подпись "В месяц"',      'type'=>'text'],
                    ],
                    'default' => [
                        'calc_title' => 'Калькулятор доходности',
                        'amount'     => 'Сумма инвестиций',
                        'period'     => 'Период',
                        'income'     => 'Доход',
                        'total'      => 'Итого',
                        'monthly'    => 'В месяц',
                    ],
                ],

                'process' => [
                    'label' => '👣 Как стать инвестором (4 шага)',
                    'icon'  => '👣',
                    'fields' => [
                        ['key'=>'process_badge',    'label'=>'Бейдж секции',    'type'=>'text'],
                        ['key'=>'process_title',    'label'=>'Заголовок',       'type'=>'text'],
                        ['key'=>'process_title_hl', 'label'=>'Акцент заголовка','type'=>'text'],
                        ['key'=>'process_sub',      'label'=>'Подзаголовок',    'type'=>'text'],
                        ['key'=>'step1_title',      'label'=>'Шаг 1: название', 'type'=>'text'],
                        ['key'=>'step1_desc',       'label'=>'Шаг 1: описание', 'type'=>'textarea'],
                        ['key'=>'step2_title',      'label'=>'Шаг 2: название', 'type'=>'text'],
                        ['key'=>'step2_desc',       'label'=>'Шаг 2: описание', 'type'=>'textarea'],
                        ['key'=>'step3_title',      'label'=>'Шаг 3: название', 'type'=>'text'],
                        ['key'=>'step3_desc',       'label'=>'Шаг 3: описание', 'type'=>'textarea'],
                        ['key'=>'step4_title',      'label'=>'Шаг 4: название', 'type'=>'text'],
                        ['key'=>'step4_desc',       'label'=>'Шаг 4: описание', 'type'=>'textarea'],
                    ],
                    'default' => [
                        'process_badge'    => 'Как стать инвестором',
                        'process_title'    => 'Четыре шага',
                        'process_title_hl' => 'к первому доходу',
                        'process_sub'      => 'Простой и прозрачный путь от первой встречи до регулярных выплат',
                        'step1_title'      => 'Консультация',
                        'step1_desc'       => 'Знакомимся с вашими целями, объясняем все условия и подбираем подходящую программу',
                        'step2_title'      => 'Выбор программы',
                        'step2_desc'       => 'Определяем объём, тип инвестиции и срок под ваш финансовый профиль',
                        'step3_title'      => 'Подписание договора',
                        'step3_desc'       => 'Нотариально заверенный договор с полной юридической защитой прав инвестора',
                        'step4_title'      => 'Получение дохода',
                        'step4_desc'       => 'Ежеквартальные выплаты или рост стоимости актива — по вашему выбору',
                    ],
                ],

                'guarantees' => [
                    'label' => '🛡️ Гарантии (4 пункта)',
                    'icon'  => '🛡️',
                    'fields' => [
                        ['key'=>'guar_badge',   'label'=>'Бейдж',         'type'=>'text'],
                        ['key'=>'guar_title',   'label'=>'Заголовок',     'type'=>'text'],
                        ['key'=>'guar_title_hl','label'=>'Акцент',        'type'=>'text'],
                        ['key'=>'guar_sub',     'label'=>'Подзаголовок',  'type'=>'text'],
                        ['key'=>'guar1_title',  'label'=>'Гарантия 1: заголовок', 'type'=>'text'],
                        ['key'=>'guar1_text',   'label'=>'Гарантия 1: текст',     'type'=>'textarea'],
                        ['key'=>'guar2_title',  'label'=>'Гарантия 2: заголовок', 'type'=>'text'],
                        ['key'=>'guar2_text',   'label'=>'Гарантия 2: текст',     'type'=>'textarea'],
                        ['key'=>'guar3_title',  'label'=>'Гарантия 3: заголовок', 'type'=>'text'],
                        ['key'=>'guar3_text',   'label'=>'Гарантия 3: текст',     'type'=>'textarea'],
                        ['key'=>'guar4_title',  'label'=>'Гарантия 4: заголовок', 'type'=>'text'],
                        ['key'=>'guar4_text',   'label'=>'Гарантия 4: текст',     'type'=>'textarea'],
                    ],
                    'default' => [
                        'guar_badge'    => 'Наши гарантии',
                        'guar_title'    => 'Защита',
                        'guar_title_hl' => 'на каждом этапе',
                        'guar_sub'      => 'Юридические, финансовые и репутационные гарантии для каждого инвестора',
                        'guar1_title'   => 'Нотариальный договор',
                        'guar1_text'    => 'Каждая сделка оформляется нотариально и регистрируется в государственных органах',
                        'guar2_title'   => 'Фиксация цены',
                        'guar2_text'    => 'Стоимость и доходность закрепляются в договоре и не меняются в течение всего срока',
                        'guar3_title'   => 'Ежеквартальная отчётность',
                        'guar3_text'    => 'Вы получаете полный отчёт о состоянии проекта и вашего портфеля каждые три месяца',
                        'guar4_title'   => 'Персональный менеджер',
                        'guar4_text'    => 'Выделенный специалист сопровождает ваш портфель на протяжении всего срока инвестирования',
                    ],
                ],

                'faq' => [
                    'label' => '❓ Вопросы и ответы (5 FAQ)',
                    'icon'  => '❓',
                    'fields' => [
                        ['key'=>'faq_badge', 'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'faq_title', 'label'=>'Заголовок',    'type'=>'text'],
                        ['key'=>'faq_sub',   'label'=>'Подзаголовок', 'type'=>'text'],
                        ['key'=>'q1', 'label'=>'Вопрос 1', 'type'=>'text'],
                        ['key'=>'a1', 'label'=>'Ответ 1',  'type'=>'textarea'],
                        ['key'=>'q2', 'label'=>'Вопрос 2', 'type'=>'text'],
                        ['key'=>'a2', 'label'=>'Ответ 2',  'type'=>'textarea'],
                        ['key'=>'q3', 'label'=>'Вопрос 3', 'type'=>'text'],
                        ['key'=>'a3', 'label'=>'Ответ 3',  'type'=>'textarea'],
                        ['key'=>'q4', 'label'=>'Вопрос 4', 'type'=>'text'],
                        ['key'=>'a4', 'label'=>'Ответ 4',  'type'=>'textarea'],
                        ['key'=>'q5', 'label'=>'Вопрос 5', 'type'=>'text'],
                        ['key'=>'a5', 'label'=>'Ответ 5',  'type'=>'textarea'],
                    ],
                    'default' => [
                        'faq_badge' => 'Вопросы и ответы',
                        'faq_title' => 'Часто задаваемые вопросы',
                        'faq_sub'   => 'Не нашли ответ? Напишите нам — ответим в течение одного рабочего дня',
                        'q1' => 'Какой минимальный порог входа?',
                        'a1' => 'Минимальная сумма зависит от программы: покупка квартиры — от $30 000, долевое участие — от $50 000, коммерческие площади — от $25 000.',
                        'q2' => 'Как и когда выплачивается доход?',
                        'a2' => 'Доход выплачивается ежеквартально на ваш банковский счёт. Вы выбираете валюту выплат: USD, TJS или RUB.',
                        'q3' => 'Можно ли вывести средства досрочно?',
                        'a3' => 'Досрочный выход предусмотрен договором. Условия и возможные комиссии оговариваются индивидуально.',
                        'q4' => 'Какие документы нужны для старта?',
                        'a4' => 'Потребуется паспорт, ИНН (для резидентов РТ) и подтверждение источника средств.',
                        'q5' => 'Можно ли реинвестировать доход или увеличить долю?',
                        'a5' => 'Да, по истечении первого периода вы можете реинвестировать прибыль или увеличить вложения.',
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════════════
            // SERVICES — t('services.*')
            // ═══════════════════════════════════════════════════════════════
            'services' => [

                'hero' => [
                    'label' => '🎯 Заголовок страницы',
                    'icon'  => '🎯',
                    'fields' => [
                        ['key'=>'badge',    'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'title',    'label'=>'Заголовок H1', 'type'=>'text'],
                        ['key'=>'subtitle', 'label'=>'Подзаголовок', 'type'=>'textarea'],
                        ['key'=>'stat1_v',  'label'=>'Стат 1: значение', 'type'=>'text'],
                        ['key'=>'stat1_l',  'label'=>'Стат 1: подпись',  'type'=>'text'],
                        ['key'=>'stat2_v',  'label'=>'Стат 2: значение', 'type'=>'text'],
                        ['key'=>'stat2_l',  'label'=>'Стат 2: подпись',  'type'=>'text'],
                        ['key'=>'stat3_v',  'label'=>'Стат 3: значение', 'type'=>'text'],
                        ['key'=>'stat3_l',  'label'=>'Стат 3: подпись',  'type'=>'text'],
                        ['key'=>'stat4_v',  'label'=>'Стат 4: значение', 'type'=>'text'],
                        ['key'=>'stat4_l',  'label'=>'Стат 4: подпись',  'type'=>'text'],
                    ],
                    'default' => [
                        'badge'    => 'Наши услуги',
                        'title'    => 'Полный спектр услуг',
                        'subtitle' => 'От покупки квартиры до управления инвестиционным портфелем',
                        'stat1_v'  => '15+',  'stat1_l' => 'Лет на рынке',
                        'stat2_v'  => '50+',  'stat2_l' => 'Объектов сдано',
                        'stat3_v'  => '5 лет','stat3_l' => 'Гарантия на работы',
                        'stat4_v'  => '24/7', 'stat4_l' => 'Поддержка после сдачи',
                    ],
                ],

                'build' => [
                    'label' => '🏗️ Три направления',
                    'icon'  => '🏗️',
                    'fields' => [
                        ['key'=>'build_badge', 'label'=>'Бейдж',         'type'=>'text'],
                        ['key'=>'build_title', 'label'=>'Заголовок',     'type'=>'text'],
                        ['key'=>'build_sub',   'label'=>'Подзаголовок',  'type'=>'textarea'],
                    ],
                    'default' => [
                        'build_badge' => 'Что мы строим',
                        'build_title' => 'Три направления. Один стандарт качества.',
                        'build_sub'   => 'Каждое направление опирается на 15-летний опыт и единую производственную базу.',
                    ],
                ],

                'services_list' => [
                    'label' => '🛠️ Услуги 1–3',
                    'icon'  => '🛠️',
                    'fields' => [
                        ['key'=>'svc1_title', 'label'=>'Услуга 1: заголовок', 'type'=>'text'],
                        ['key'=>'svc1_desc',  'label'=>'Услуга 1: описание',  'type'=>'textarea'],
                        ['key'=>'svc1_i1',    'label'=>'Услуга 1: пункт 1',   'type'=>'text'],
                        ['key'=>'svc1_i2',    'label'=>'Услуга 1: пункт 2',   'type'=>'text'],
                        ['key'=>'svc1_i3',    'label'=>'Услуга 1: пункт 3',   'type'=>'text'],
                        ['key'=>'svc1_i4',    'label'=>'Услуга 1: пункт 4',   'type'=>'text'],
                        ['key'=>'svc2_title', 'label'=>'Услуга 2: заголовок', 'type'=>'text'],
                        ['key'=>'svc2_desc',  'label'=>'Услуга 2: описание',  'type'=>'textarea'],
                        ['key'=>'svc2_i1',    'label'=>'Услуга 2: пункт 1',   'type'=>'text'],
                        ['key'=>'svc2_i2',    'label'=>'Услуга 2: пункт 2',   'type'=>'text'],
                        ['key'=>'svc2_i3',    'label'=>'Услуга 2: пункт 3',   'type'=>'text'],
                        ['key'=>'svc2_i4',    'label'=>'Услуга 2: пункт 4',   'type'=>'text'],
                        ['key'=>'svc3_title', 'label'=>'Услуга 3: заголовок', 'type'=>'text'],
                        ['key'=>'svc3_desc',  'label'=>'Услуга 3: описание',  'type'=>'textarea'],
                        ['key'=>'svc3_i1',    'label'=>'Услуга 3: пункт 1',   'type'=>'text'],
                        ['key'=>'svc3_i2',    'label'=>'Услуга 3: пункт 2',   'type'=>'text'],
                        ['key'=>'svc3_i3',    'label'=>'Услуга 3: пункт 3',   'type'=>'text'],
                        ['key'=>'svc3_i4',    'label'=>'Услуга 3: пункт 4',   'type'=>'text'],
                    ],
                    'default' => [
                        'svc1_title' => 'Жилищное строительство',
                        'svc1_desc'  => 'Проектирование и строительство жилых комплексов премиум-класса.',
                        'svc1_i1'    => 'Многоквартирные дома',  'svc1_i2' => 'Элитные апартаменты',
                        'svc1_i3'    => 'Умный дом под ключ',    'svc1_i4' => 'Благоустройство территории',
                        'svc2_title' => 'Коммерческая недвижимость',
                        'svc2_desc'  => 'Офисные центры, торговые площади и гостиницы.',
                        'svc2_i1'    => 'Бизнес-центры класса А','svc2_i2' => 'Торговые галереи',
                        'svc2_i3'    => 'Гостиничные комплексы', 'svc2_i4' => 'Складская логистика',
                        'svc3_title' => 'Инфраструктурные проекты',
                        'svc3_desc'  => 'Сложные инженерные объекты, опираясь на производственную базу.',
                        'svc3_i1'    => 'Дороги и развязки',     'svc3_i2' => 'Инженерные сети',
                        'svc3_i3'    => 'Промышленные объекты',  'svc3_i4' => 'Геодезия и изыскания',
                    ],
                ],

                'process' => [
                    'label' => '⚙️ Процесс работы (6 шагов)',
                    'icon'  => '⚙️',
                    'fields' => [
                        ['key'=>'proc_badge',    'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'proc_title',    'label'=>'Заголовок',    'type'=>'text'],
                        ['key'=>'proc_title_hl', 'label'=>'Акцент',       'type'=>'text'],
                        ['key'=>'proc_sub',      'label'=>'Подзаголовок', 'type'=>'textarea'],
                        ['key'=>'step1_title',   'label'=>'Шаг 1: название', 'type'=>'text'],
                        ['key'=>'step1_desc',    'label'=>'Шаг 1: описание', 'type'=>'textarea'],
                        ['key'=>'step2_title',   'label'=>'Шаг 2: название', 'type'=>'text'],
                        ['key'=>'step2_desc',    'label'=>'Шаг 2: описание', 'type'=>'textarea'],
                        ['key'=>'step3_title',   'label'=>'Шаг 3: название', 'type'=>'text'],
                        ['key'=>'step3_desc',    'label'=>'Шаг 3: описание', 'type'=>'textarea'],
                        ['key'=>'step4_title',   'label'=>'Шаг 4: название', 'type'=>'text'],
                        ['key'=>'step4_desc',    'label'=>'Шаг 4: описание', 'type'=>'textarea'],
                        ['key'=>'step5_title',   'label'=>'Шаг 5: название', 'type'=>'text'],
                        ['key'=>'step5_desc',    'label'=>'Шаг 5: описание', 'type'=>'textarea'],
                        ['key'=>'step6_title',   'label'=>'Шаг 6: название', 'type'=>'text'],
                        ['key'=>'step6_desc',    'label'=>'Шаг 6: описание', 'type'=>'textarea'],
                    ],
                    'default' => [
                        'proc_badge'    => 'Как мы работаем',
                        'proc_title'    => 'От идеи до ключей —',
                        'proc_title_hl' => 'без лишних шагов',
                        'proc_sub'      => 'Шесть этапов с чёткими сроками и прозрачной отчётностью на каждом.',
                        'step1_title'   => 'Анализ и консультация',
                        'step1_desc'    => 'Изучаем требования, анализируем участок и рыночные условия.',
                        'step2_title'   => 'Проектирование',
                        'step2_desc'    => 'Архитектурный проект, инженерные решения и полный пакет сметной документации.',
                        'step3_title'   => 'Согласование',
                        'step3_desc'    => 'Полное сопровождение получения разрешительной документации от госорганов.',
                        'step4_title'   => 'Строительство',
                        'step4_desc'    => 'Реализация с жёстким контролем качества, сроков и бюджета. Еженедельная отчётность.',
                        'step5_title'   => 'Сдача объекта',
                        'step5_desc'    => 'Передача ключей, подключение умных систем, обучение персонала.',
                        'step6_title'   => 'Послепродажный сервис',
                        'step6_desc'    => 'Гарантия 5 лет, собственная УК, техническая поддержка 24/7.',
                    ],
                ],

                'smart' => [
                    'label' => '🏠 Умный дом',
                    'icon'  => '🏠',
                    'fields' => [
                        ['key'=>'smart_badge', 'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'smart_title', 'label'=>'Заголовок',    'type'=>'text'],
                        ['key'=>'smart_sub',   'label'=>'Подзаголовок', 'type'=>'textarea'],
                        ['key'=>'hl1_title',   'label'=>'Хайлайт 1: заголовок', 'type'=>'text'],
                        ['key'=>'hl1_desc',    'label'=>'Хайлайт 1: описание',  'type'=>'textarea'],
                        ['key'=>'hl2_title',   'label'=>'Хайлайт 2: заголовок', 'type'=>'text'],
                        ['key'=>'hl2_desc',    'label'=>'Хайлайт 2: описание',  'type'=>'textarea'],
                        ['key'=>'hl3_title',   'label'=>'Хайлайт 3: заголовок', 'type'=>'text'],
                        ['key'=>'hl3_desc',    'label'=>'Хайлайт 3: описание',  'type'=>'textarea'],
                    ],
                    'default' => [
                        'smart_badge' => 'Технологии',
                        'smart_title' => 'Умный дом',
                        'smart_sub'   => 'Интегрированные IoT-решения управляют климатом, безопасностью и освещением.',
                        'hl1_title'   => 'Единое мобильное приложение',
                        'hl1_desc'    => 'iOS и Android. Управление всеми системами из одной точки.',
                        'hl2_title'   => 'Интеграция с камерами',
                        'hl2_desc'    => 'Видеонаблюдение 24/7 с хранением в облаке и доступом в реальном времени.',
                        'hl3_title'   => 'Автоматизация климата',
                        'hl3_desc'    => 'Умный термостат обучается вашему расписанию и снижает расходы на 30%.',
                    ],
                ],

                'infra' => [
                    'label' => '🏙️ Инфраструктура',
                    'icon'  => '🏙️',
                    'fields' => [
                        ['key'=>'infra_badge',    'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'infra_title_1',  'label'=>'Заголовок 1',  'type'=>'text'],
                        ['key'=>'infra_title_2',  'label'=>'Заголовок 2',  'type'=>'text'],
                        ['key'=>'infra_sub',      'label'=>'Подзаголовок', 'type'=>'textarea'],
                        ['key'=>'infra1_title',   'label'=>'Объект 1: название', 'type'=>'text'],
                        ['key'=>'infra1_desc',    'label'=>'Объект 1: описание',  'type'=>'text'],
                        ['key'=>'infra2_title',   'label'=>'Объект 2: название', 'type'=>'text'],
                        ['key'=>'infra2_desc',    'label'=>'Объект 2: описание',  'type'=>'text'],
                        ['key'=>'infra3_title',   'label'=>'Объект 3: название', 'type'=>'text'],
                        ['key'=>'infra3_desc',    'label'=>'Объект 3: описание',  'type'=>'text'],
                        ['key'=>'infra4_title',   'label'=>'Объект 4: название', 'type'=>'text'],
                        ['key'=>'infra4_desc',    'label'=>'Объект 4: описание',  'type'=>'text'],
                        ['key'=>'infra5_title',   'label'=>'Объект 5: название', 'type'=>'text'],
                        ['key'=>'infra5_desc',    'label'=>'Объект 5: описание',  'type'=>'text'],
                        ['key'=>'infra6_title',   'label'=>'Объект 6: название', 'type'=>'text'],
                        ['key'=>'infra6_desc',    'label'=>'Объект 6: описание',  'type'=>'text'],
                    ],
                    'default' => [
                        'infra_badge'   => 'Инфраструктура',
                        'infra_title_1' => 'Всё необходимое',
                        'infra_title_2' => 'в одном месте',
                        'infra_sub'     => 'Спорт, паркинг, магазины, детская зона и озеленённый двор.',
                        'infra1_title'  => 'Бассейн',         'infra1_desc' => 'Крытый подогреваемый, 25 м',
                        'infra2_title'  => 'Фитнес-зал',      'infra2_desc' => '800 м², современное оборудование',
                        'infra3_title'  => 'Озеленённый парк','infra3_desc' => 'Закрытая территория 1,2 га',
                        'infra4_title'  => 'Подземный паркинг','infra4_desc' => '200 машиномест, видеонаблюдение',
                        'infra5_title'  => 'Коммерческий этаж','infra5_desc' => 'Магазины, кофейни, сервисы',
                        'infra6_title'  => 'Детская площадка', 'infra6_desc' => 'Сертифицированная, безопасная зона',
                    ],
                ],

                'cta' => [
                    'label' => '📣 CTA (низ страницы)',
                    'icon'  => '📣',
                    'fields' => [
                        ['key'=>'cta_badge',   'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'cta_title_1', 'label'=>'Заголовок 1',  'type'=>'text'],
                        ['key'=>'cta_title_2', 'label'=>'Заголовок 2',  'type'=>'text'],
                        ['key'=>'cta_sub',     'label'=>'Подзаголовок', 'type'=>'textarea'],
                        ['key'=>'cta_btn1',    'label'=>'Кнопка 1',     'type'=>'text'],
                        ['key'=>'cta_btn2',    'label'=>'Кнопка 2',     'type'=>'text'],
                        ['key'=>'g1_v',   'label'=>'Гарантия 1: значение', 'type'=>'text'],
                        ['key'=>'g1_l',   'label'=>'Гарантия 1: подпись',  'type'=>'text'],
                        ['key'=>'g2_v',   'label'=>'Гарантия 2: значение', 'type'=>'text'],
                        ['key'=>'g2_l',   'label'=>'Гарантия 2: подпись',  'type'=>'text'],
                        ['key'=>'g3_v',   'label'=>'Гарантия 3: значение', 'type'=>'text'],
                        ['key'=>'g3_l',   'label'=>'Гарантия 3: подпись',  'type'=>'text'],
                        ['key'=>'g4_v',   'label'=>'Гарантия 4: значение', 'type'=>'text'],
                        ['key'=>'g4_l',   'label'=>'Гарантия 4: подпись',  'type'=>'text'],
                    ],
                    'default' => [
                        'cta_badge'   => 'Начнём?',
                        'cta_title_1' => 'Нужна консультация',
                        'cta_title_2' => 'по вашему проекту?',
                        'cta_sub'     => 'Расскажите о задаче — наш менеджер свяжется в течение 30 минут.',
                        'cta_btn1'    => 'Обсудить проект',
                        'cta_btn2'    => 'Смотреть объекты',
                        'g1_v'  => '5 лет',   'g1_l'  => 'Гарантия на строительные работы',
                        'g2_v'  => '30 мин',  'g2_l'  => 'Ответ менеджера после заявки',
                        'g3_v'  => '0%',      'g3_l'  => 'Скрытых платежей в договоре',
                        'g4_v'  => '24/7',    'g4_l'  => 'Техподдержка после сдачи объекта',
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════════════
            // PROGRESS — t('progress.*')
            // ═══════════════════════════════════════════════════════════════
            'progress' => [

                'hero' => [
                    'label' => '🎯 Заголовок страницы',
                    'icon'  => '🎯',
                    'fields' => [
                        ['key'=>'badge',    'label'=>'Бейдж',        'type'=>'text'],
                        ['key'=>'title',    'label'=>'Заголовок H1', 'type'=>'text'],
                        ['key'=>'subtitle', 'label'=>'Подзаголовок', 'type'=>'textarea'],
                        ['key'=>'readiness','label'=>'Подпись прогресса', 'type'=>'text'],
                        ['key'=>'as_of',    'label'=>'Подпись даты', 'type'=>'text'],
                        ['key'=>'start_date','label'=>'Дата начала', 'type'=>'text'],
                        ['key'=>'end_date',  'label'=>'Дата сдачи',  'type'=>'text'],
                    ],
                    'default' => [
                        'badge'      => 'Ход строительства',
                        'title'      => 'Стройка онлайн',
                        'subtitle'   => 'Следите за ходом строительства в реальном времени',
                        'readiness'  => 'Готовность объекта',
                        'as_of'      => 'по состоянию на',
                        'start_date' => 'Начало — Июль 2022',
                        'end_date'   => 'Сдача — 2027',
                    ],
                ],

                'phases' => [
                    'label' => '📊 Фазы строительства (6 пунктов)',
                    'icon'  => '📊',
                    'fields' => [
                        ['key'=>'ph1_name', 'label'=>'Фаза 1', 'type'=>'text'],
                        ['key'=>'ph2_name', 'label'=>'Фаза 2', 'type'=>'text'],
                        ['key'=>'ph3_name', 'label'=>'Фаза 3', 'type'=>'text'],
                        ['key'=>'ph4_name', 'label'=>'Фаза 4', 'type'=>'text'],
                        ['key'=>'ph5_name', 'label'=>'Фаза 5', 'type'=>'text'],
                        ['key'=>'ph6_name', 'label'=>'Фаза 6', 'type'=>'text'],
                    ],
                    'default' => [
                        'ph1_name' => 'Нулевой цикл (фундамент)',
                        'ph2_name' => 'Монолитный каркас',
                        'ph3_name' => 'Фасадные работы',
                        'ph4_name' => 'Внутренняя отделка',
                        'ph5_name' => 'Инженерные системы',
                        'ph6_name' => 'Благоустройство территории',
                    ],
                ],

                'site_stats' => [
                    'label' => '🏗️ Показатели строительства',
                    'icon'  => '🏗️',
                    'fields' => [
                        ['key'=>'ss1_v', 'label'=>'Стат 1: значение', 'type'=>'text'],
                        ['key'=>'ss1_l', 'label'=>'Стат 1: подпись',  'type'=>'text'],
                        ['key'=>'ss2_v', 'label'=>'Стат 2: значение', 'type'=>'text'],
                        ['key'=>'ss2_l', 'label'=>'Стат 2: подпись',  'type'=>'text'],
                        ['key'=>'ss3_v', 'label'=>'Стат 3: значение', 'type'=>'text'],
                        ['key'=>'ss3_l', 'label'=>'Стат 3: подпись',  'type'=>'text'],
                        ['key'=>'ss4_v', 'label'=>'Стат 4: значение', 'type'=>'text'],
                        ['key'=>'ss4_l', 'label'=>'Стат 4: подпись',  'type'=>'text'],
                    ],
                    'default' => [
                        'ss1_v' => '12',   'ss1_l' => 'Башен в работе',
                        'ss2_v' => '480',  'ss2_l' => 'Рабочих на объекте',
                        'ss3_v' => '34',   'ss3_l' => 'Этажей завершено',
                        'ss4_v' => '2027', 'ss4_l' => 'Год сдачи',
                    ],
                ],

                'updates' => [
                    'label' => '📰 Обновления хода стройки (5 записей)',
                    'icon'  => '📰',
                    'fields' => [
                        ['key'=>'upd1_date',  'label'=>'Обновление 1: дата',      'type'=>'text'],
                        ['key'=>'upd1_title', 'label'=>'Обновление 1: заголовок', 'type'=>'text'],
                        ['key'=>'upd1_desc',  'label'=>'Обновление 1: описание',  'type'=>'textarea'],
                        ['key'=>'upd2_date',  'label'=>'Обновление 2: дата',      'type'=>'text'],
                        ['key'=>'upd2_title', 'label'=>'Обновление 2: заголовок', 'type'=>'text'],
                        ['key'=>'upd2_desc',  'label'=>'Обновление 2: описание',  'type'=>'textarea'],
                        ['key'=>'upd3_date',  'label'=>'Обновление 3: дата',      'type'=>'text'],
                        ['key'=>'upd3_title', 'label'=>'Обновление 3: заголовок', 'type'=>'text'],
                        ['key'=>'upd3_desc',  'label'=>'Обновление 3: описание',  'type'=>'textarea'],
                        ['key'=>'upd4_date',  'label'=>'Обновление 4: дата',      'type'=>'text'],
                        ['key'=>'upd4_title', 'label'=>'Обновление 4: заголовок', 'type'=>'text'],
                        ['key'=>'upd4_desc',  'label'=>'Обновление 4: описание',  'type'=>'textarea'],
                        ['key'=>'upd5_date',  'label'=>'Обновление 5: дата',      'type'=>'text'],
                        ['key'=>'upd5_title', 'label'=>'Обновление 5: заголовок', 'type'=>'text'],
                        ['key'=>'upd5_desc',  'label'=>'Обновление 5: описание',  'type'=>'textarea'],
                    ],
                    'default' => [
                        'upd1_date'  => 'Май 2026',
                        'upd1_title' => 'Монолитный каркас — башни 1–4',
                        'upd1_desc'  => 'Завершены работы по 15-му этажу башен 1 и 2. Башня 3 достигла 12-го этажа.',
                        'upd2_date'  => 'Апрель 2026',
                        'upd2_title' => 'Каркас +3 этажа, установка лифтовых шахт',
                        'upd2_desc'  => 'Забетонированы перекрытия 12–14 этажей. Установлены лифтовые направляющие.',
                        'upd3_date'  => 'Март 2026',
                        'upd3_title' => 'Начало фасадных работ первой очереди',
                        'upd3_desc'  => 'Смонтированы первые секции вентилируемого фасада на этажах 1–5 башни 1.',
                        'upd4_date'  => 'Декабрь 2025',
                        'upd4_title' => 'Фундамент завершён по всем башням',
                        'upd4_desc'  => 'Полностью завершён нулевой цикл для всех 12 башен комплекса.',
                        'upd5_date'  => 'Июль 2022',
                        'upd5_title' => 'Старт строительства QUDRAT CITY',
                        'upd5_desc'  => 'Официальная закладка первого камня. Начало земляных и свайных работ.',
                    ],
                ],

                'milestones' => [
                    'label' => '🏁 Основные этапы (4 вехи)',
                    'icon'  => '🏁',
                    'fields' => [
                        ['key'=>'ms1_title',  'label'=>'Веха 1: название', 'type'=>'text'],
                        ['key'=>'ms1_desc',   'label'=>'Веха 1: описание', 'type'=>'textarea'],
                        ['key'=>'ms1_period', 'label'=>'Веха 1: период',   'type'=>'text'],
                        ['key'=>'ms2_title',  'label'=>'Веха 2: название', 'type'=>'text'],
                        ['key'=>'ms2_desc',   'label'=>'Веха 2: описание', 'type'=>'textarea'],
                        ['key'=>'ms2_period', 'label'=>'Веха 2: период',   'type'=>'text'],
                        ['key'=>'ms3_title',  'label'=>'Веха 3: название', 'type'=>'text'],
                        ['key'=>'ms3_desc',   'label'=>'Веха 3: описание', 'type'=>'textarea'],
                        ['key'=>'ms3_period', 'label'=>'Веха 3: период',   'type'=>'text'],
                        ['key'=>'ms4_title',  'label'=>'Веха 4: название', 'type'=>'text'],
                        ['key'=>'ms4_desc',   'label'=>'Веха 4: описание', 'type'=>'textarea'],
                        ['key'=>'ms4_period', 'label'=>'Веха 4: период',   'type'=>'text'],
                    ],
                    'default' => [
                        'ms1_title'  => 'Нулевой цикл',
                        'ms1_desc'   => 'Фундамент, подземная парковка, инженерные коммуникации.',
                        'ms1_period' => '2022–2023',
                        'ms2_title'  => 'Монолитный каркас',
                        'ms2_desc'   => 'Все этажи всех башен. Лифтовые шахты, перекрытия.',
                        'ms2_period' => '2023–2025',
                        'ms3_title'  => 'Фасад и отделка',
                        'ms3_desc'   => 'Вентилируемый фасад, остекление, внутренняя чистовая отделка.',
                        'ms3_period' => '2025–2026',
                        'ms4_title'  => 'Сдача и заселение',
                        'ms4_desc'   => 'Государственная комиссия, передача ключей владельцам.',
                        'ms4_period' => '2027',
                    ],
                ],

                'subscribe' => [
                    'label' => '📧 Подписка на обновления',
                    'icon'  => '📧',
                    'fields' => [
                        ['key'=>'subscribe_title', 'label'=>'Заголовок',   'type'=>'text'],
                        ['key'=>'sub_btn',         'label'=>'Кнопка',      'type'=>'text'],
                        ['key'=>'sub_placeholder', 'label'=>'Плейсхолдер', 'type'=>'text'],
                    ],
                    'default' => [
                        'subscribe_title' => 'Подписаться на обновления',
                        'sub_btn'         => 'Подписаться',
                        'sub_placeholder' => 'Ваш e-mail',
                    ],
                ],
            ],
        ];
    }

    // ── Index ─────────────────────────────────────────────────────────────
    public function index(): Response
    {
        $schemas = $this->getSchemas();
        $stats   = [];
        foreach ($this->pages as $key => $info) {
            $db_count     = PageSection::where('page', $key)->count();
            $schema_count = count($schemas[$key] ?? []);
            $stats[$key]  = [
                'label'         => $info['label'],
                'icon'          => $info['icon'],
                'sections'      => $db_count,
                'schema_total'  => $schema_count,
                'locales'       => PageSection::where('page', $key)->distinct('locale')->pluck('locale'),
            ];
        }

        return Inertia::render('Admin/Cms/Index', ['pages' => $stats]);
    }

    // ── Page editor ───────────────────────────────────────────────────────
    public function page(string $page): Response
    {
        abort_unless(array_key_exists($page, $this->pages), 404);

        $dbSections = PageSection::where('page', $page)
            ->orderBy('position')
            ->get()
            ->groupBy('locale');

        // Merge DB data with schema defaults
        $schemas  = $this->getSchemas();
        $schema   = $schemas[$page] ?? [];

        $sections = [];
        foreach (['ru','en','tj'] as $locale) {
            $localeRows = ($dbSections[$locale] ?? collect())->keyBy('section');
            $sections[$locale] = [];

            // First: predefined schema sections
            foreach ($schema as $sectionKey => $schemaDef) {
                $row = $localeRows[$sectionKey] ?? null;
                $sections[$locale][$sectionKey] = [
                    'id'         => $row?->id,
                    'content'    => $row ? ($row->content ?? []) : [],
                    'settings'   => $row ? ($row->settings ?? []) : [],
                    'is_active'  => $row ? $row->is_active : false,
                    'position'   => $row?->position ?? 0,
                    'updated_at' => $row?->updated_at?->format('d.m.Y H:i'),
                    'in_db'      => (bool) $row,
                    'schema'     => $schemaDef,
                ];
            }

            // Then: any extra sections in DB not in schema
            foreach ($localeRows as $sectionKey => $row) {
                if (!isset($sections[$locale][$sectionKey])) {
                    $sections[$locale][$sectionKey] = [
                        'id'         => $row->id,
                        'content'    => $row->content ?? [],
                        'settings'   => $row->settings ?? [],
                        'is_active'  => $row->is_active,
                        'position'   => $row->position ?? 0,
                        'updated_at' => $row->updated_at?->format('d.m.Y H:i'),
                        'in_db'      => true,
                        'schema'     => null,
                    ];
                }
            }
        }

        return Inertia::render('Admin/Cms/Page', [
            'page'     => $page,
            'label'    => $this->pages[$page]['label'],
            'sections' => $sections,
            'locales'  => ['ru','en','tj'],
        ]);
    }

    // ── Upsert section ────────────────────────────────────────────────────
    public function upsertSection(Request $request, string $page)
    {
        Gate::authorize('manage');
        abort_unless(array_key_exists($page, $this->pages), 404);

        $data = $request->validate([
            'section'   => 'required|string|max:80',
            'locale'    => 'required|in:ru,en,tj',
            'content'   => 'nullable|array',
            'settings'  => 'nullable|array',
            'is_active' => 'boolean',
            'position'  => 'integer|min:0',
        ]);

        $section = PageSection::updateOrCreate(
            ['page' => $page, 'section' => $data['section'], 'locale' => $data['locale']],
            [
                'content'    => $data['content']  ?? [],
                'settings'   => $data['settings'] ?? [],
                'is_active'  => $data['is_active'] ?? true,
                'position'   => $data['position']  ?? 0,
                'updated_by' => auth()->id(),
            ]
        );

        Cache::forget("page_sections_{$page}_{$data['locale']}");
        Cache::forget("cms_flat_{$page}_{$data['locale']}");
        AuditLog::record('updated', 'CMS', "Секция [{$data['section']}] страницы [{$page}] ({$data['locale']}) обновлена");

        return back()->with('success', 'Секция сохранена.');
    }

    // ── Toggle active ─────────────────────────────────────────────────────
    public function toggleSection(Request $request, string $page)
    {
        Gate::authorize('manage');
        abort_unless(array_key_exists($page, $this->pages), 404);

        $data = $request->validate([
            'section'   => 'required|string|max:80',
            'locale'    => 'required|in:ru,en,tj',
            'is_active' => 'required|boolean',
        ]);

        PageSection::updateOrCreate(
            ['page' => $page, 'section' => $data['section'], 'locale' => $data['locale']],
            ['is_active' => $data['is_active'], 'updated_by' => auth()->id()]
        );

        Cache::forget("page_sections_{$page}_{$data['locale']}");
        Cache::forget("cms_flat_{$page}_{$data['locale']}");
        return response()->json(['ok' => true]);
    }

    // ── Init page with defaults ───────────────────────────────────────────
    public function initPage(Request $request, string $page)
    {
        Gate::authorize('manage');
        abort_unless(array_key_exists($page, $this->pages), 404);

        $locale  = $request->input('locale', 'ru');
        $schemas = $this->getSchemas();
        $schema  = $schemas[$page] ?? [];
        $created = 0;

        // Pre-load existing RU sections so non-RU locales can copy from them
        $ruSections = $locale !== 'ru'
            ? PageSection::where('page', $page)->where('locale', 'ru')
                ->pluck('content', 'section')->toArray()
            : [];

        foreach ($schema as $sectionKey => $def) {
            $exists = PageSection::where('page', $page)
                ->where('section', $sectionKey)
                ->where('locale', $locale)
                ->exists();

            if (!$exists) {
                // For non-RU locales: copy RU content as starting point for translation
                if ($locale !== 'ru' && isset($ruSections[$sectionKey])) {
                    $content = $ruSections[$sectionKey];
                } else {
                    $content = $def['default'] ?? [];
                }

                PageSection::create([
                    'page'       => $page,
                    'section'    => $sectionKey,
                    'locale'     => $locale,
                    'content'    => $content,
                    'is_active'  => true,
                    'position'   => array_search($sectionKey, array_keys($schema)),
                    'updated_by' => auth()->id(),
                ]);
                $created++;
            }
        }

        Cache::forget("page_sections_{$page}_{$locale}");
        Cache::forget("cms_flat_{$page}_{$locale}");

        return back()->with('success', "Создано {$created} секций из шаблона.");
    }

    // ── Delete section ────────────────────────────────────────────────────
    public function destroySection(PageSection $section)
    {
        Gate::authorize('delete-record');
        Cache::forget("page_sections_{$section->page}_{$section->locale}");
        Cache::forget("cms_flat_{$section->page}_{$section->locale}");
        $section->delete();
        return back()->with('success', 'Секция удалена.');
    }
}
