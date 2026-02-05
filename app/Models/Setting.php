<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    public function getTypedValueAttribute(): mixed
    {
        return match ($this->type) {
            'int' => (int) $this->value,
            'float' => (float) $this->value,
            'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            $setting = self::find($key);
            return $setting ? $setting->typedValue : $default;
        });
    }

    public static function setValue(string $key, mixed $value, string $type = 'string', ?string $description = null): void
    {
        $stringValue = match ($type) {
            'json' => json_encode($value),
            'bool' => $value ? 'true' : 'false',
            default => (string) $value,
        };

        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("setting:{$key}");
    }
}
