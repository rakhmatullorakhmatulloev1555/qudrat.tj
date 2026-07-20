<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text | textarea | boolean | image | url
            $table->string('group')->default('general'); // general | contact | social | seo
            $table->string('label');
            $table->timestamps();
        });

        // Seed default settings
        $now = now();
        DB::table('site_settings')->insert([
            // general
            ['key'=>'site_name',       'value'=>'QUDRAT',                                         'type'=>'text',    'group'=>'general', 'label'=>'Название сайта',       'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'site_tagline',    'value'=>'Умный жилой комплекс в Таджикистане',             'type'=>'text',    'group'=>'general', 'label'=>'Слоган',               'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'site_description','value'=>'Первый умный жилой комплекс с гарантией качества','type'=>'textarea','group'=>'general', 'label'=>'Описание',             'created_at'=>$now,'updated_at'=>$now],
            // contact
            ['key'=>'contact_phone',   'value'=>'+992 000 000 000',                                'type'=>'text',    'group'=>'contact', 'label'=>'Телефон',              'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'contact_email',   'value'=>'info@qudrat.tj',                                  'type'=>'text',    'group'=>'contact', 'label'=>'Email',                'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'contact_address', 'value'=>'Душанбе, Таджикистан',                            'type'=>'textarea','group'=>'contact', 'label'=>'Адрес',                'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'work_hours',      'value'=>'Пн–Пт 09:00–18:00, Сб 10:00–15:00',              'type'=>'text',    'group'=>'contact', 'label'=>'Часы работы',          'created_at'=>$now,'updated_at'=>$now],
            // social
            ['key'=>'social_telegram', 'value'=>'https://t.me/qudrat_tj',                          'type'=>'url',     'group'=>'social',  'label'=>'Telegram',             'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'social_whatsapp', 'value'=>'https://wa.me/992000000000',                      'type'=>'url',     'group'=>'social',  'label'=>'WhatsApp',             'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'social_instagram','value'=>'https://instagram.com/qudrat_tj',                 'type'=>'url',     'group'=>'social',  'label'=>'Instagram',            'created_at'=>$now,'updated_at'=>$now],
            // seo
            ['key'=>'seo_title',       'value'=>'QUDRAT — Умный жилой комплекс',                   'type'=>'text',    'group'=>'seo',     'label'=>'SEO Заголовок',        'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'seo_description', 'value'=>'QUDRAT — первый умный ЖК в Таджикистане.',        'type'=>'textarea','group'=>'seo',     'label'=>'SEO Описание',         'created_at'=>$now,'updated_at'=>$now],
            ['key'=>'seo_keywords',    'value'=>'квартиры Душанбе, умный дом, новостройка',        'type'=>'text',    'group'=>'seo',     'label'=>'SEO Ключевые слова',   'created_at'=>$now,'updated_at'=>$now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
