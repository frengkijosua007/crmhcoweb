<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Get a setting value
     */
    public static function get($key, $default = null)
    {
        $settings = self::getAllSettings();

        return $settings[$key] ?? $default;
    }

    /**
     * Get all settings
     */
    public static function getAllSettings()
    {
        return Cache::remember('settings', 60 * 24, function () {
            return Setting::getAllSettings();
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::forget('settings');
    }

    /**
     * Get company logo URL
     */
    public static function getCompanyLogo()
    {
        $logo = self::get('company_logo');

        if ($logo && file_exists(public_path('storage/' . $logo))) {
            return asset('storage/' . $logo);
        }

        return asset('images/default-logo.png');
    }

    /**
     * Format date according to settings
     */
    public static function formatDate($date)
    {
        if (!$date) {
            return null;
        }

        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        $format = self::get('date_format', 'd M Y');

        return $date->format($format);
    }

    /**
     * Format time according to settings
     */
    public static function formatTime($time)
    {
        if (!$time) {
            return null;
        }

        if (is_string($time)) {
            $time = new \DateTime($time);
        }

        $format = self::get('time_format', 'H:i');

        return $time->format($format);
    }

    /**
     * Format datetime according to settings
     */
    public static function formatDateTime($datetime)
    {
        if (!$datetime) {
            return null;
        }

        if (is_string($datetime)) {
            $datetime = new \DateTime($datetime);
        }

        $dateFormat = self::get('date_format', 'd M Y');
        $timeFormat = self::get('time_format', 'H:i');

        return $datetime->format("$dateFormat $timeFormat");
    }

    /**
     * Format currency according to settings
     */
    public static function formatCurrency($amount)
    {
        $currency = self::get('currency', 'IDR');

        switch ($currency) {
            case 'IDR':
                return 'Rp ' . number_format($amount, 0, ',', '.');
            case 'USD':
                return '$ ' . number_format($amount, 2, '.', ',');
            default:
                return $amount;
        }
    }
}
