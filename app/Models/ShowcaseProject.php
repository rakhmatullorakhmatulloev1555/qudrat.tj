<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Знаковый объект (блок «Знаковые объекты» на /objects + страница /projects/{slug}).
 *
 * content — переводимый контент вида:
 * {
 *   ru: { type_label, location, card_desc, tagline, about1, about2, status_label,
 *         stats: [{v,l} x3], features: [{title,desc} x4] },
 *   tj: {...}, en: {...}
 * }
 *
 * seo — {
 *   meta_title: {ru,tj,en}, meta_description: {ru,tj,en},
 *   og_title: {ru,tj,en}, og_description: {ru,tj,en},
 *   og_image, canonical_url, schema_type, schema_json
 * }
 */
class ShowcaseProject extends Model
{
    protected $fillable = [
        'slug', 'name', 'accent', 'hero_image', 'gallery', 'feature_icons',
        'content', 'seo', 'cta_type', 'status', 'is_featured', 'is_visible',
        'sort_order', 'published_at',
    ];

    protected $casts = [
        'gallery'       => 'array',
        'feature_icons' => 'array',
        'content'       => 'array',
        'seo'           => 'array',
        'is_featured'   => 'boolean',
        'is_visible'    => 'boolean',
        'published_at'  => 'datetime',
    ];

    public const STATUSES  = ['draft', 'published', 'archived'];
    public const CTA_TYPES = ['apts', 'contact', 'mining'];

    // ── Scopes ──

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // ── Helpers ──

    /** Уникальный slug из имени. */
    public static function makeSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name) ?: 'project';
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    /** Локализованное значение из content с фолбэком на RU. */
    public function loc(string $key, string $locale = 'ru')
    {
        $val = data_get($this->content, "{$locale}.{$key}");
        if ($val === null || $val === '' || $val === []) {
            $val = data_get($this->content, "ru.{$key}");
        }
        return $val;
    }

    /** Локализованное SEO-поле (per-locale подобъект) с фолбэком на RU. */
    public function seoLoc(string $key, string $locale = 'ru'): ?string
    {
        $val = data_get($this->seo, "{$key}.{$locale}");
        if ($val === null || $val === '') {
            $val = data_get($this->seo, "{$key}.ru");
        }
        return $val ?: null;
    }

    /** Данные для карточки в блоке «Знаковые объекты». */
    public function toCard(string $locale = 'ru'): array
    {
        return [
            'slug'   => $this->slug,
            'name'   => $this->name,
            'img'    => $this->hero_image,
            'accent' => $this->accent,
            'type'   => $this->loc('type_label', $locale),
            'loc'    => $this->loc('location', $locale),
            'desc'   => $this->loc('card_desc', $locale),
            'stats'  => collect($this->loc('stats', $locale) ?? [])->take(3)->values()->all(),
        ];
    }

    /** Полные данные для страницы /projects/{slug}. */
    public function toPage(string $locale = 'ru'): array
    {
        $features = collect($this->loc('features', $locale) ?? [])->take(4)->values();
        $icons    = collect($this->feature_icons ?? []);

        return [
            'slug'        => $this->slug,
            'name'        => $this->name,
            'accent'      => $this->accent,
            'hero'        => $this->hero_image,
            'gallery'     => array_values($this->gallery ?? []),
            'type'        => $this->loc('type_label', $locale),
            'loc'         => $this->loc('location', $locale),
            'tag'         => $this->loc('tagline', $locale),
            'about1'      => $this->loc('about1', $locale),
            'about2'      => $this->loc('about2', $locale),
            'statusLabel' => $this->loc('status_label', $locale),
            'stats'       => collect($this->loc('stats', $locale) ?? [])->take(3)->values()->all(),
            'features'    => $features->map(fn($f, $i) => [
                'icon'  => $icons[$i] ?? 'building',
                'title' => $f['title'] ?? '',
                'desc'  => $f['desc'] ?? '',
            ])->all(),
            'ctaType'     => $this->cta_type,
            'seo'         => [
                'metaTitle'       => $this->seoLoc('meta_title', $locale) ?: $this->name,
                'metaDescription' => $this->seoLoc('meta_description', $locale) ?: (string) $this->loc('tagline', $locale),
                'ogTitle'         => $this->seoLoc('og_title', $locale),
                'ogDescription'   => $this->seoLoc('og_description', $locale),
                'ogImage'         => data_get($this->seo, 'og_image') ?: $this->hero_image,
                'canonical'       => data_get($this->seo, 'canonical_url'),
                'schema'          => $this->schemaLd($locale),
            ],
        ];
    }

    /** JSON-LD (Schema.org) для страницы проекта. */
    public function schemaLd(string $locale = 'ru'): ?array
    {
        $custom = data_get($this->seo, 'schema_json');
        if (is_string($custom) && $custom !== '') {
            $decoded = json_decode($custom, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $type = data_get($this->seo, 'schema_type') ?: 'ApartmentComplex';
        if ($type === 'none') {
            return null;
        }

        return array_filter([
            '@context'    => 'https://schema.org',
            '@type'       => $type,
            'name'        => $this->name,
            'description' => (string) $this->loc('tagline', $locale),
            'image'       => $this->hero_image ? url($this->hero_image) : null,
            'url'         => url('/projects/' . $this->slug),
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => (string) $this->loc('location', $locale),
                'addressCountry'  => 'TJ',
            ],
        ]);
    }
}
