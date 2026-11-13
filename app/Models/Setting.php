<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Configurações editáveis do site (chave/valor).
 * Valores padrão ficam em config/site.php; o que o gestor salva no admin sobrescreve.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public const CACHE_KEY = 'site_settings';

    /** Todas as configurações (padrões + banco), com cache. */
    public static function allValues(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $defaults = config('site.defaults', []);

            try {
                if (! Schema::hasTable('settings')) {
                    return $defaults;
                }
                $stored = static::query()->pluck('value', 'key')
                    ->map(fn ($v) => self::decode($v))
                    ->all();
            } catch (\Throwable) {
                return $defaults;
            }

            // Campo salvo vazio no painel = usa o valor padrão de config/site.php
            $stored = array_filter($stored, fn ($v) => $v !== null && $v !== '' && $v !== []);

            return array_replace($defaults, $stored);
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::allValues()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], [
                'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value,
            ]);
        }
        Cache::forget(self::CACHE_KEY);
    }

    private static function decode(?string $value): mixed
    {
        if ($value !== null && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return $value;
    }
}
