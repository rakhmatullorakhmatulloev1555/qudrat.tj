<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Client;
use App\Models\MiningShipment;
use App\Models\Partner;
use App\Models\Document;
use App\Models\Apartment;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Leads (заявки за последние 6 месяцев) ──
        $sources   = ['website', 'phone', 'referral', 'social', 'other'];
        $statuses  = ['new', 'new', 'new', 'in_progress', 'success', 'rejected'];
        $interests = ['apartment', '2-комнатная', 'коммерческая', 'инвестиции', null];

        $names = [
            ['Алишер Мухаммадов', '+992501111001'],
            ['Сарвиноз Рахимова',  '+992501111002'],
            ['Бахром Юсупов',      '+992501111003'],
            ['Нилуфар Каримова',   '+992501111004'],
            ['Отабек Хасанов',     '+992501111005'],
            ['Зарина Абдуллаева',  '+992501111006'],
            ['Фарход Мирзоев',     '+992501111007'],
            ['Дилором Тошматова',  '+992501111008'],
            ['Санжар Бобоев',      '+992501111009'],
            ['Мадина Насирова',    '+992501111010'],
            ['John Smith',         '+79161234567'],
            ['Rustam Nazarov',     '+992921234567'],
            ['Камол Раджабов',     '+992501111013'],
            ['Феруза Хайдарова',   '+992501111014'],
            ['Тимур Ахмедов',      '+992501111015'],
        ];

        foreach ($names as $i => [$name, $phone]) {
            $monthsAgo = rand(0, 5);
            $daysAgo   = rand(0, 27);
            Lead::create([
                'name'       => $name,
                'phone'      => $phone,
                'email'      => strtolower(str_replace(' ', '.', $name)) . '@test.com',
                'status'     => $statuses[array_rand($statuses)],
                'source'     => $sources[array_rand($sources)],
                'interest'   => $interests[array_rand($interests)],
                'message'    => 'Интересует покупка квартиры. Прошу связаться.',
                'created_at' => now()->subMonths($monthsAgo)->subDays($daysAgo),
                'updated_at' => now()->subMonths($monthsAgo)->subDays($daysAgo),
            ]);
        }

        // ── Clients ──
        $clients = [
            ['Алишер Мухаммадов', '+992501111001', 'Душанбе, ул. Рудаки 12'],
            ['Sandro Ferrari',     '+390612345678', 'Milano, Italy'],
            ['Иван Петров',        '+79161234568',  'Москва, Россия'],
            ['Бахром Юсупов',      '+992501111003', 'Душанбе, пр. Исмоили Сомони 5'],
            ['Aziz Karimov',       '+998901234567', 'Ташкент, Узбекистан'],
        ];
        foreach ($clients as [$name, $phone, $address]) {
            Client::firstOrCreate(['phone' => $phone], [
                'name'    => $name,
                'phone'   => $phone,
                'address' => $address,
                'type'    => 'individual',
                'status'  => 'active',
                'source'  => 'website',
            ]);
        }

        // ── Mining Shipments (партии за 6 месяцев) ──
        $coalTypes   = ['energy', 'coking', 'anthracite'];
        $mStatuses   = ['planned', 'loading', 'shipped', 'delivered', 'paid'];
        $buyers      = ['Tajik Energy LLC', 'Steel Corp Россия', 'Uzbek Metal Plant', 'China Import Co', 'Euro Energy GmbH'];
        $destinations = ['Душанбе', 'Ташкент', 'Москва', 'Шанхай', 'Берлин'];

        for ($m = 5; $m >= 0; $m--) {
            $shipmentsThisMonth = rand(2, 5);
            for ($j = 0; $j < $shipmentsThisMonth; $j++) {
                $type = $coalTypes[array_rand($coalTypes)];
                $vol  = rand(500, 3000);
                MiningShipment::create([
                    'date'          => now()->subMonths($m)->addDays(rand(0, 25))->format('Y-m-d'),
                    'site'          => 'Шахта №' . rand(1, 5) . ', Исфара',
                    'coal_type'     => $type,
                    'volume'        => $vol,
                    'price_per_ton' => rand(45, 95),
                    'currency'      => 'USD',
                    'buyer'         => $buyers[array_rand($buyers)],
                    'destination'   => $destinations[array_rand($destinations)],
                    'status'        => $m > 1 ? $mStatuses[array_rand([3,4,4])] : $mStatuses[array_rand($mStatuses)],
                    'quality_kcal'  => $type === 'anthracite' ? rand(7000, 8200) : rand(5000, 6500),
                    'notes'         => null,
                ]);
            }
        }

        // ── Partners ──
        $partners = [
            ['Tajik Steel LLC',    'TJ', 'buyer',       'Рустам Назаров', '+992921111001',  500000,  '2021-03-15'],
            ['China Import Co',    'CN', 'buyer',       'Wang Wei',        '+8613812345678', 1200000, '2022-01-10'],
            ['Euro Energy GmbH',   'DE', 'investor',    'Hans Müller',     '+4930123456',    800000,  '2023-06-01'],
            ['Узметкомбинат',      'UZ', 'buyer',       'Азиз Каримов',    '+998712345678',  650000,  '2020-09-20'],
            ['AUCHAN Logistics',   'RU', 'contractor',  'Иван Сидоров',    '+74951234567',   300000,  '2024-02-01'],
        ];
        foreach ($partners as [$name, $country, $type, $contact, $phone, $vol, $since]) {
            Partner::firstOrCreate(['name' => $name], [
                'name'             => $name,
                'country'          => $country,
                'type'             => $type,
                'contact_person'   => $contact,
                'phone'            => $phone,
                'annual_volume'    => $vol,
                'currency'         => 'USD',
                'partnership_since'=> $since,
                'contract_status'  => 'active',
                'is_active'        => true,
            ]);
        }

        // ── Documents ──
        $docTypes = [
            ['Устав ООО КУДРАТ',               'other',       'active',  now()->addYears(5)],
            ['Лицензия на горнодобычу',         'permit',      'active',  now()->addMonths(8)],
            ['Договор с China Import Co',        'contract',    'active',  now()->addMonths(3)],
            ['Разрешение на строительство ЖК',  'permit',      'active',  now()->addMonths(18)],
            ['Сертификат ISO 9001',              'certificate', 'active',  now()->addDays(20)],
            ['Технические условия угля ТУ-2024','other',       'active',  now()->addYears(2)],
            ['Договор аренды офиса',            'contract',    'active',  now()->addMonths(6)],
            ['Проект планировки ЖК (черновик)', 'other',       'draft',   null],
        ];
        foreach ($docTypes as [$title, $type, $status, $expires]) {
            Document::firstOrCreate(['title' => $title], [
                'title'        => $title,
                'type'         => $type,
                'status'       => $status,
                'related_type' => 'company',
                'related_name' => 'QUDRAT LLC',
                'signed_by'    => 'Директор Мухаммадов А.',
                'issued_at'    => now()->subYear(),
                'expires_at'   => $expires,
                'notes'        => null,
            ]);
        }

        // ── Extra apartments ──
        $projectId = Project::first()?->id ?? 1;
        $aptNum    = 100;
        for ($r = 1; $r <= 4; $r++) {
            for ($f = 1; $f <= 5; $f++) {
                $aptNum++;
                Apartment::firstOrCreate(
                    ['project_id' => $projectId, 'number' => (string)$aptNum],
                    [
                        'project_id' => $projectId,
                        'number'     => (string)$aptNum,
                        'rooms'      => $r,
                        'area'       => $r * 25 + rand(0, 15),
                        'floor'      => $f,
                        'price'      => $r * 15000 + rand(0, 5000),
                        'currency'   => 'USD',
                        'finish'     => ['none', 'rough', 'fine', 'furnished'][rand(0, 3)],
                        'status'     => ['available', 'available', 'available', 'reserved', 'sold'][rand(0, 4)],
                    ]
                );
            }
        }

        $this->command->info('✅ Тестовые данные успешно загружены!');
        $this->command->table(
            ['Модель', 'Записей'],
            [
                ['Leads',    Lead::count()],
                ['Clients',  Client::count()],
                ['Mining',   MiningShipment::count()],
                ['Partners', Partner::count()],
                ['Documents',Document::count()],
                ['Apartments',Apartment::count()],
            ]
        );
    }
}
