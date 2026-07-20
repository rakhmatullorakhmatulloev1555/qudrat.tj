<?php

namespace App\Services;

use App\Models\ApartmentScene;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Scene3DStorage — единое соглашение о хранении 3D-ассетов.
 *
 * Структура (диск public):
 *   3d/apartment_{id}/
 *     models/    ← GLB/GLTF
 *     textures/  ← JPG/PNG/WEBP/KTX2
 *     hdri/      ← HDR/HDRI
 *     config/    ← JSON-конфиги
 *
 * Позволяет админу загружать ассеты без изменения кода —
 * пути формируются здесь централизованно.
 */
class Scene3DStorage
{
    public const DISK = 'public';
    public const KINDS = ['model' => 'models', 'texture' => 'textures', 'hdri' => 'hdri', 'material_map' => 'textures'];

    public static function base(int $apartmentId): string
    {
        return "3d/apartment_{$apartmentId}";
    }

    /** Создать структуру папок при включении сцены. */
    public static function ensureStructure(ApartmentScene $scene): void
    {
        $disk = Storage::disk(self::DISK);
        $base = self::base($scene->apartment_id);
        foreach (['models', 'textures', 'hdri', 'config'] as $dir) {
            $disk->makeDirectory("{$base}/{$dir}");
        }
    }

    /** Сохранить загруженный файл в нужную подпапку по kind. */
    public static function store(UploadedFile $file, int $apartmentId, string $kind): array
    {
        $sub = self::KINDS[$kind] ?? 'misc';
        $path = $file->store(self::base($apartmentId) . "/{$sub}", self::DISK);

        return [
            'path'       => $path,
            'disk'       => self::DISK,
            'size_bytes' => $file->getSize(),
            'format'     => strtolower($file->getClientOriginalExtension()),
        ];
    }

    /** Разрешённые расширения по kind (для валидации загрузки). */
    public static function allowedExtensions(string $kind): array
    {
        return match ($kind) {
            'model'        => ['glb', 'gltf'],
            'texture', 'material_map' => ['jpg', 'jpeg', 'png', 'webp', 'ktx2'],
            'hdri'         => ['hdr', 'exr'],
            default        => [],
        };
    }
}
