<?php

/*
|--------------------------------------------------------------------------
| Контактные данные компании
|--------------------------------------------------------------------------
| env() здесь безопасен: значения читаются только при построении кеша
| конфига (php artisan config:cache), а не на каждый HTTP-запрос.
| В рантайме используйте config('contacts.*').
*/

return [
    'phone'    => env('CONTACT_PHONE',    '+992 00 000 00 00'),
    'whatsapp' => env('CONTACT_WHATSAPP', '992000000000'),
    'telegram' => env('CONTACT_TELEGRAM', 'qudrat_tj'),
    'email'    => env('CONTACT_EMAIL',    'info@qudrat.tj'),
    'address'  => env('CONTACT_ADDRESS',  'Душанбе, Таджикистан'),
];
