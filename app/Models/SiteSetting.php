<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
    }

    /**
     * Get all settings as key => value array.
     */
    public static function allKeyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Get all settings grouped by group.
     */
    public static function grouped(): array
    {
        return static::orderBy('group')->orderBy('id')->get()
            ->groupBy('group')
            ->map(fn($g) => $g->keyBy('key'))
            ->toArray();
    }
}
