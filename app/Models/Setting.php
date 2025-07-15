<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', 'value', 'group', 'type'
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return self::formatValue($setting->value, $setting->type);
    }

    /**
     * Get all settings as an array
     */
    public static function getAllSettings()
    {
        return Cache::remember('settings', 60 * 24, function () {
            $settings = self::all();

            $formattedSettings = [];
            foreach ($settings as $setting) {
                $formattedSettings[$setting->key] = self::formatValue($setting->value, $setting->type);
            }

            return $formattedSettings;
        });
    }

    /**
     * Format the value based on type
     */
    private static function formatValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
                return json_decode($value, true);
            case 'object':
                return json_decode($value);
            default:
                return $value;
        }
    }
}
