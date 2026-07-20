<?php

namespace Database\Seeders;

use App\Models\ShowcaseProject;
use Illuminate\Database\Seeder;

/**
 * Переносит 4 знаковых объекта (ранее — статика в lang/{ru,tj,en}/site.php)
 * в таблицу showcase_projects. Повторный запуск безопасен (updateOrCreate).
 */
class ShowcaseProjectSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->projects() as $i => $data) {
            $data['sort_order']   = $i;
            $data['status']       = 'published';
            $data['published_at'] = now();
            ShowcaseProject::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }

    private function projects(): array
    {
        return [
            // ═══ QUDRAT RESIDENCE ═══
            [
                'slug'          => 'qudrat-residence',
                'name'          => 'QUDRAT RESIDENCE',
                'accent'        => '#C9A96E',
                'hero_image'    => '/images/hero-3.jpg',
                'gallery'       => ['/images/hero-4.jpg', '/images/hero-6.jpg', '/images/hero-8.jpg'],
                'feature_icons' => ['mountain', 'phone', 'sparkles', 'key'],
                'cta_type'      => 'apts',
                'is_featured'   => true,
                'is_visible'    => true,
                'content'       => [
                    'ru' => [
                        'type_label'   => 'Премиум',
                        'location'     => 'Душанбе, Таджикистан',
                        'card_desc'    => 'Элитный жилой комплекс бизнес-класса',
                        'tagline'      => 'Элитная резиденция бизнес-класса в престижном районе Душанбе',
                        'status_label' => 'В продаже',
                        'about1'       => 'QUDRAT RESIDENCE — элитный жилой комплекс бизнес-класса в сердце Душанбе. Архитектура мирового уровня, панорамное остекление и приватная закрытая территория задают новый стандарт городской жизни в Таджикистане.',
                        'about2'       => 'Каждая деталь — от дизайнерских входных лобби до интеллектуальных инженерных систем — продумана для тех, кто ценит комфорт без компромиссов. Квартиры от 45 м² с чистовой отделкой и системой «умный дом».',
                        'stats'        => [
                            ['v' => 'от 45 м²', 'l' => 'Площадь'],
                            ['v' => '2025',     'l' => 'Сдача'],
                            ['v' => '$1,200',   'l' => 'За м²'],
                        ],
                        'features'     => [
                            ['title' => 'Панорамные виды',      'desc' => 'Окна в пол с видами на горы и вечерний город'],
                            ['title' => 'Умный дом',            'desc' => 'Управление климатом, светом и безопасностью со смартфона'],
                            ['title' => 'Приватная территория', 'desc' => 'Закрытый двор с ландшафтным парком и детскими зонами'],
                            ['title' => 'Подземный паркинг',    'desc' => 'Отапливаемый паркинг с лифтом до вашего этажа'],
                        ],
                    ],
                    'tj' => [
                        'type_label'   => 'Премиум',
                        'location'     => 'Душанбе, Тоҷикистон',
                        'card_desc'    => 'Маҷмааи истиқоматии элитаи синфи бизнес',
                        'tagline'      => 'Резиденсияи элитаи синфи бизнес дар ноҳияи бонуфузи Душанбе',
                        'status_label' => 'Дар фурӯш',
                        'about1'       => 'QUDRAT RESIDENCE — маҷмааи истиқоматии элитаи синфи бизнес дар маркази Душанбе. Меъмории дараҷаи ҷаҳонӣ, шишабандии панорамӣ ва ҳудуди пӯшидаи хусусӣ стандарти нави зиндагии шаҳриро дар Тоҷикистон муқаррар мекунанд.',
                        'about2'       => 'Ҳар як ҷузъиёт — аз лобби-ҳои дизайнерӣ то системаҳои муҳандисии интеллектуалӣ — барои онҳое андешида шудааст, ки роҳатро бе созиш қадр мекунанд. Хонаҳо аз 45 м² бо пардозиши тайёр ва системаи «хонаи оқил».',
                        'stats'        => [
                            ['v' => 'аз 45 м²', 'l' => 'Масоҳат'],
                            ['v' => '2025',     'l' => 'Таҳвил'],
                            ['v' => '$1,200',   'l' => 'Барои м²'],
                        ],
                        'features'     => [
                            ['title' => 'Манзараҳои панорамӣ',    'desc' => 'Тирезаҳо то фарш бо манзараи кӯҳҳо ва шаҳри шомгоҳӣ'],
                            ['title' => 'Хонаи оқил',             'desc' => 'Идораи иқлим, рӯшноӣ ва бехатарӣ аз смартфон'],
                            ['title' => 'Ҳудуди хусусӣ',          'desc' => 'Ҳавлии пӯшида бо боғи ландшафтӣ ва майдончаҳои кӯдакона'],
                            ['title' => 'Таваққуфгоҳи зеризаминӣ','desc' => 'Таваққуфгоҳи гарм бо лифт то ошёнаи шумо'],
                        ],
                    ],
                    'en' => [
                        'type_label'   => 'Premium',
                        'location'     => 'Dushanbe, Tajikistan',
                        'card_desc'    => 'Elite business-class residential complex',
                        'tagline'      => 'Elite business-class residence in a prestigious district of Dushanbe',
                        'status_label' => 'On Sale',
                        'about1'       => 'QUDRAT RESIDENCE is an elite business-class residential complex in the heart of Dushanbe. World-class architecture, panoramic glazing and a private gated grounds set a new standard of urban living in Tajikistan.',
                        'about2'       => 'Every detail — from designer entrance lobbies to intelligent engineering systems — is crafted for those who value comfort without compromise. Apartments from 45 m² with premium finishing and smart-home systems.',
                        'stats'        => [
                            ['v' => '45+ m²', 'l' => 'Area'],
                            ['v' => '2025',   'l' => 'Delivery'],
                            ['v' => '$1,200', 'l' => 'Per m²'],
                        ],
                        'features'     => [
                            ['title' => 'Panoramic Views',     'desc' => 'Floor-to-ceiling windows overlooking the mountains and city lights'],
                            ['title' => 'Smart Home',          'desc' => 'Control climate, lighting and security from your smartphone'],
                            ['title' => 'Private Grounds',     'desc' => "Gated courtyard with a landscaped park and children's areas"],
                            ['title' => 'Underground Parking', 'desc' => 'Heated parking with elevator access to your floor'],
                        ],
                    ],
                ],
                'seo' => $this->defaultSeo(),
            ],

            // ═══ QUDRAT BUSINESS CENTER ═══
            [
                'slug'          => 'qudrat-business-center',
                'name'          => 'QUDRAT BUSINESS CENTER',
                'accent'        => '#60a5fa',
                'hero_image'    => '/images/hero-5.jpg',
                'gallery'       => ['/images/hero-9.jpg', '/images/hero-10.jpg', '/images/hero-11.jpg'],
                'feature_icons' => ['building', 'cog', 'users', 'shield'],
                'cta_type'      => 'contact',
                'is_featured'   => true,
                'is_visible'    => true,
                'content'       => [
                    'ru' => [
                        'type_label'   => 'Бизнес',
                        'location'     => 'Душанбе, Таджикистан',
                        'card_desc'    => 'Современный бизнес-центр класса A+ в деловом центре',
                        'tagline'      => 'Современный бизнес-центр класса A+ в деловом центре столицы',
                        'status_label' => 'Строится',
                        'about1'       => 'QUDRAT BUSINESS CENTER — деловое пространство международного уровня в центре Душанбе. Офисы класса A+ с гибкими планировками от 100 м², рассчитанные на компании любого масштаба — от представительств до штаб-квартир.',
                        'about2'       => 'Интеллектуальная система управления зданием (BMS), скоростные лифты, конференц-залы и сервисная инфраструктура создают среду, в которой бизнес работает эффективнее.',
                        'stats'        => [
                            ['v' => 'от 100 м²', 'l' => 'Площадь'],
                            ['v' => '2026',      'l' => 'Сдача'],
                            ['v' => '$1,800',    'l' => 'За м²'],
                        ],
                        'features'     => [
                            ['title' => 'Класс A+',         'desc' => 'Офисные пространства по международным стандартам'],
                            ['title' => 'BMS-система',      'desc' => 'Интеллектуальное управление зданием 24/7'],
                            ['title' => 'Конференц-центр',  'desc' => 'Залы для переговоров, презентаций и мероприятий'],
                            ['title' => 'Паркинг и сервис', 'desc' => 'Многоуровневый паркинг, ресепшн и охрана'],
                        ],
                    ],
                    'tj' => [
                        'type_label'   => 'Бизнес',
                        'location'     => 'Душанбе, Тоҷикистон',
                        'card_desc'    => 'Маркази тиҷории синфи A+ дар маркази шаҳр',
                        'tagline'      => 'Маркази тиҷории муосири синфи A+ дар маркази корчаллонии пойтахт',
                        'status_label' => 'Дар сохтмон',
                        'about1'       => 'QUDRAT BUSINESS CENTER — фазои корчаллонии сатҳи байналмилалӣ дар маркази Душанбе. Офисҳои синфи A+ бо тарҳбандии чандир аз 100 м² барои ширкатҳои ҳар миқёс — аз намояндагиҳо то қароргоҳҳо.',
                        'about2'       => 'Системаи интеллектуалии идораи бино (BMS), лифтҳои тезгард, толорҳои конфронс ва инфраструктураи хизматрасонӣ муҳите месозанд, ки дар он бизнес самараноктар кор мекунад.',
                        'stats'        => [
                            ['v' => 'аз 100 м²', 'l' => 'Масоҳат'],
                            ['v' => '2026',      'l' => 'Таҳвил'],
                            ['v' => '$1,800',    'l' => 'Барои м²'],
                        ],
                        'features'     => [
                            ['title' => 'Синфи A+',              'desc' => 'Фазоҳои офисӣ аз рӯи стандартҳои байналмилалӣ'],
                            ['title' => 'Системаи BMS',          'desc' => 'Идораи интеллектуалии бино 24/7'],
                            ['title' => 'Маркази конфронс',      'desc' => 'Толорҳо барои музокирот, презентатсия ва чорабиниҳо'],
                            ['title' => 'Таваққуфгоҳ ва сервис', 'desc' => 'Таваққуфгоҳи бисёрошёна, ресепшн ва муҳофиза'],
                        ],
                    ],
                    'en' => [
                        'type_label'   => 'Business',
                        'location'     => 'Dushanbe, Tajikistan',
                        'card_desc'    => 'Class A+ business centre in the downtown district',
                        'tagline'      => "Modern Class A+ business centre in the capital's downtown district",
                        'status_label' => 'Under Construction',
                        'about1'       => 'QUDRAT BUSINESS CENTER is an international-grade business space in central Dushanbe. Class A+ offices with flexible layouts from 100 m², designed for companies of any scale — from representative offices to headquarters.',
                        'about2'       => 'An intelligent building management system (BMS), high-speed elevators, conference halls and service infrastructure create an environment where business runs more efficiently.',
                        'stats'        => [
                            ['v' => '100+ m²', 'l' => 'Area'],
                            ['v' => '2026',    'l' => 'Delivery'],
                            ['v' => '$1,800',  'l' => 'Per m²'],
                        ],
                        'features'     => [
                            ['title' => 'Class A+',          'desc' => 'Office spaces built to international standards'],
                            ['title' => 'BMS System',        'desc' => 'Intelligent building management around the clock'],
                            ['title' => 'Conference Centre', 'desc' => 'Halls for negotiations, presentations and events'],
                            ['title' => 'Parking & Service', 'desc' => 'Multi-level parking, reception and security'],
                        ],
                    ],
                ],
                'seo' => $this->defaultSeo(),
            ],

            // ═══ QUDRAT CITY ═══
            [
                'slug'          => 'qudrat-city',
                'name'          => 'QUDRAT CITY',
                'accent'        => '#22c55e',
                'hero_image'    => '/images/hero-7.jpg',
                'gallery'       => ['/images/hero-12.jpg', '/images/hero-14.jpg', '/images/hero-15.jpg'],
                'feature_icons' => ['building', 'sparkles', 'clipboard', 'truck'],
                'cta_type'      => 'apts',
                'is_featured'   => true,
                'is_visible'    => true,
                'content'       => [
                    'ru' => [
                        'type_label'   => 'Комфорт',
                        'location'     => 'Душанбе, Таджикистан',
                        'card_desc'    => 'Масштабный жилой район с развитой инфраструктурой',
                        'tagline'      => 'Масштабный жилой район с полной городской инфраструктурой',
                        'status_label' => 'Строится',
                        'about1'       => 'QUDRAT CITY — флагманский проект холдинга: целый жилой район, где всё необходимое для жизни находится в пешей доступности. Школы, детские сады, магазины, парки и спортивные площадки — внутри квартала.',
                        'about2'       => 'Продуманные планировки от компактных студий до просторных семейных квартир делают QUDRAT CITY доступным для каждого, кто хочет жить в новом стандарте комфорта.',
                        'stats'        => [
                            ['v' => 'от 35 м²', 'l' => 'Площадь'],
                            ['v' => '2026',     'l' => 'Сдача'],
                            ['v' => '$950',     'l' => 'За м²'],
                        ],
                        'features'     => [
                            ['title' => 'Полная инфраструктура',    'desc' => 'Школы, детские сады и магазины внутри района'],
                            ['title' => 'Зелёные зоны',             'desc' => 'Парки, аллеи и прогулочные бульвары'],
                            ['title' => 'Планировки для каждого',   'desc' => 'Квартиры от студий до семейных форматов'],
                            ['title' => 'Транспортная доступность', 'desc' => 'Удобные выезды на ключевые магистрали города'],
                        ],
                    ],
                    'tj' => [
                        'type_label'   => 'Комфорт',
                        'location'     => 'Душанбе, Тоҷикистон',
                        'card_desc'    => 'Ноҳияи истиқоматии калон бо инфраструктураи пурра',
                        'tagline'      => 'Ноҳияи истиқоматии калон бо инфраструктураи пурраи шаҳрӣ',
                        'status_label' => 'Дар сохтмон',
                        'about1'       => 'QUDRAT CITY — лоиҳаи флагмании холдинг: ноҳияи томи истиқоматӣ, ки дар он ҳама чизи барои зиндагӣ зарурӣ дар масофаи пиёдагард аст. Мактабҳо, боғчаҳо, мағозаҳо, боғҳо ва майдончаҳои варзишӣ — дар дохили маҳалла.',
                        'about2'       => 'Тарҳбандиҳои андешидашуда — аз студияҳои ихчам то хонаҳои васеи оилавӣ — QUDRAT CITY-ро барои ҳар касе, ки мехоҳад дар стандарти нави роҳат зиндагӣ кунад, дастрас мегардонанд.',
                        'stats'        => [
                            ['v' => 'аз 35 м²', 'l' => 'Масоҳат'],
                            ['v' => '2026',     'l' => 'Таҳвил'],
                            ['v' => '$950',     'l' => 'Барои м²'],
                        ],
                        'features'     => [
                            ['title' => 'Инфраструктураи пурра',   'desc' => 'Мактабҳо, боғчаҳо ва мағозаҳо дар дохили ноҳия'],
                            ['title' => 'Минтақаҳои сабз',         'desc' => 'Боғҳо, хиёбонҳо ва булварҳои сайругашт'],
                            ['title' => 'Тарҳбандӣ барои ҳар кас', 'desc' => 'Хонаҳо аз студия то форматҳои оилавӣ'],
                            ['title' => 'Дастрасии нақлиётӣ',      'desc' => 'Баромадҳои қулай ба шоҳроҳҳои асосии шаҳр'],
                        ],
                    ],
                    'en' => [
                        'type_label'   => 'Comfort',
                        'location'     => 'Dushanbe, Tajikistan',
                        'card_desc'    => 'Large-scale residential district with full infrastructure',
                        'tagline'      => 'Large-scale residential district with full urban infrastructure',
                        'status_label' => 'Under Construction',
                        'about1'       => "QUDRAT CITY is the holding's flagship project: an entire residential district where everything you need is within walking distance. Schools, kindergartens, shops, parks and sports grounds — all inside the neighbourhood.",
                        'about2'       => 'Thoughtful layouts, from compact studios to spacious family apartments, make QUDRAT CITY accessible to everyone who wants to live to a new standard of comfort.',
                        'stats'        => [
                            ['v' => '35+ m²', 'l' => 'Area'],
                            ['v' => '2026',   'l' => 'Delivery'],
                            ['v' => '$950',   'l' => 'Per m²'],
                        ],
                        'features'     => [
                            ['title' => 'Full Infrastructure',  'desc' => 'Schools, kindergartens and shops inside the district'],
                            ['title' => 'Green Spaces',         'desc' => 'Parks, alleys and walking boulevards'],
                            ['title' => 'Layouts for Everyone', 'desc' => 'Apartments from studios to family formats'],
                            ['title' => 'Transport Access',     'desc' => "Convenient exits to the city's key highways"],
                        ],
                    ],
                ],
                'seo' => $this->defaultSeo(),
            ],

            // ═══ QUDRAT COAL COMPLEX ═══
            [
                'slug'          => 'qudrat-coal-complex',
                'name'          => 'QUDRAT COAL COMPLEX',
                'accent'        => '#fbbf24',
                'hero_image'    => '/images/hero-17.webp',
                'gallery'       => ['/images/mining/01.jpeg', '/images/mining/5-724x1024.jpg', '/images/mining/14-724x1024.jpg'],
                'feature_icons' => ['pick', 'truck', 'cog', 'shield'],
                'cta_type'      => 'mining',
                'is_featured'   => true,
                'is_visible'    => true,
                'content'       => [
                    'ru' => [
                        'type_label'   => 'Mining',
                        'location'     => 'Хатлонская обл., ТЖ',
                        'card_desc'    => 'Угольный комплекс полного цикла — добыча и переработка',
                        'tagline'      => 'Угольный комплекс полного цикла — добыча, переработка, логистика',
                        'status_label' => 'Запуск в 2026',
                        'about1'       => 'QUDRAT COAL COMPLEX — промышленный проект полного цикла в Хатлонской области: от добычи угля до обогащения, переработки и отгрузки потребителям. Проектная мощность — 5,2 млн тонн в год.',
                        'about2'       => 'Комплекс строится по международным стандартам промышленной безопасности и экологии (HSE) с применением техники ведущих мировых производителей и собственной логистической сетью.',
                        'stats'        => [
                            ['v' => '5.2 млн т', 'l' => 'Мощность'],
                            ['v' => '2026',      'l' => 'Запуск'],
                            ['v' => '100%',      'l' => 'Безопасность'],
                        ],
                        'features'     => [
                            ['title' => 'Полный цикл',             'desc' => 'От добычи до переработки и отгрузки продукции'],
                            ['title' => 'Собственная логистика',   'desc' => 'Автопарк и железнодорожные маршруты доставки'],
                            ['title' => 'Современная техника',     'desc' => 'Оборудование ведущих мировых производителей'],
                            ['title' => 'Безопасность и экология', 'desc' => 'Международные стандарты HSE на всех этапах'],
                        ],
                    ],
                    'tj' => [
                        'type_label'   => 'Mining',
                        'location'     => 'Вилояти Хатлон, ТҶ',
                        'card_desc'    => 'Комплекси ангиштии давраи пурра',
                        'tagline'      => 'Комплекси ангиштии давраи пурра — истихроҷ, коркард, логистика',
                        'status_label' => 'Оғоз дар 2026',
                        'about1'       => 'QUDRAT COAL COMPLEX — лоиҳаи саноатии давраи пурра дар вилояти Хатлон: аз истихроҷи ангишт то ғанисозӣ, коркард ва интиқол ба истеъмолкунандагон. Иқтидори лоиҳавӣ — 5,2 млн тонна дар сол.',
                        'about2'       => 'Комплекс аз рӯи стандартҳои байналмилалии бехатарии саноатӣ ва экология (HSE) бо истифодаи техникаи истеҳсолкунандагони пешбари ҷаҳон ва шабакаи логистикии худӣ сохта мешавад.',
                        'stats'        => [
                            ['v' => '5.2 млн т', 'l' => 'Иқтидор'],
                            ['v' => '2026',      'l' => 'Оғоз'],
                            ['v' => '100%',      'l' => 'Бехатарӣ'],
                        ],
                        'features'     => [
                            ['title' => 'Давраи пурра',         'desc' => 'Аз истихроҷ то коркард ва интиқоли маҳсулот'],
                            ['title' => 'Логистикаи худӣ',      'desc' => 'Автопарк ва масирҳои роҳиоҳании интиқол'],
                            ['title' => 'Техникаи муосир',      'desc' => 'Таҷҳизоти истеҳсолкунандагони пешбари ҷаҳон'],
                            ['title' => 'Бехатарӣ ва экология', 'desc' => 'Стандартҳои байналмилалии HSE дар ҳама марҳилаҳо'],
                        ],
                    ],
                    'en' => [
                        'type_label'   => 'Mining',
                        'location'     => 'Khatlon Region, TJ',
                        'card_desc'    => 'Full-cycle coal complex — extraction and processing',
                        'tagline'      => 'Full-cycle coal complex — extraction, processing, logistics',
                        'status_label' => 'Launching in 2026',
                        'about1'       => 'QUDRAT COAL COMPLEX is a full-cycle industrial project in the Khatlon Region: from coal extraction to enrichment, processing and delivery to customers. Design capacity — 5.2 million tonnes per year.',
                        'about2'       => "The complex is being built to international industrial safety and environmental (HSE) standards, using equipment from the world's leading manufacturers and a proprietary logistics network.",
                        'stats'        => [
                            ['v' => '5.2M t', 'l' => 'Capacity'],
                            ['v' => '2026',   'l' => 'Launch'],
                            ['v' => '100%',   'l' => 'Safety'],
                        ],
                        'features'     => [
                            ['title' => 'Full Cycle',           'desc' => 'From extraction to processing and product shipment'],
                            ['title' => 'In-House Logistics',   'desc' => 'Truck fleet and rail delivery routes'],
                            ['title' => 'Modern Equipment',     'desc' => "Machinery from the world's leading manufacturers"],
                            ['title' => 'Safety & Environment', 'desc' => 'International HSE standards at every stage'],
                        ],
                    ],
                ],
                'seo' => array_merge($this->defaultSeo(), ['schema_type' => 'Organization']),
            ],
        ];
    }

    private function defaultSeo(): array
    {
        return [
            'meta_title'       => ['ru' => '', 'tj' => '', 'en' => ''],
            'meta_description' => ['ru' => '', 'tj' => '', 'en' => ''],
            'og_title'         => ['ru' => '', 'tj' => '', 'en' => ''],
            'og_description'   => ['ru' => '', 'tj' => '', 'en' => ''],
            'og_image'         => '',
            'canonical_url'    => '',
            'schema_type'      => 'ApartmentComplex',
            'schema_json'      => '',
        ];
    }
}
