<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General Settings
        $this->createSetting('site_name', 'Project Management System', 'general', 'string');
        $this->createSetting('site_description', 'Project and survey management system', 'general', 'string');
        $this->createSetting('maintenance_mode', '0', 'general', 'boolean');
        $this->createSetting('default_pagination', '20', 'general', 'integer');
        $this->createSetting('date_format', 'd M Y', 'general', 'string');
        $this->createSetting('time_format', 'H:i', 'general', 'string');
        $this->createSetting('timezone', 'Asia/Jakarta', 'general', 'string');

        // Company Settings
        $this->createSetting('company_name', 'Your Company Name', 'company', 'string');
        $this->createSetting('company_address', 'Company Address Line 1, Line 2', 'company', 'string');
        $this->createSetting('company_phone', '+62 123 456 7890', 'company', 'string');
        $this->createSetting('company_email', 'info@company.com', 'company', 'string');
        $this->createSetting('company_website', 'https://company.com', 'company', 'string');
        $this->createSetting('company_tax_id', '123456789', 'company', 'string');
        $this->createSetting('invoice_prefix', 'INV', 'company', 'string');
        $this->createSetting('currency', 'IDR', 'company', 'string');
        $this->createSetting('fiscal_year_start', '01-01', 'company', 'string');

        // Notification Settings
        $this->createSetting('email_notifications', '1', 'notification', 'boolean');
        $this->createSetting('survey_notifications', '1', 'notification', 'boolean');
        $this->createSetting('project_status_notifications', '1', 'notification', 'boolean');
        $this->createSetting('document_upload_notifications', '1', 'notification', 'boolean');
        $this->createSetting('client_notifications', '1', 'notification', 'boolean');
        $this->createSetting('notification_email', 'notifications@company.com', 'notification', 'string');
        $this->createSetting('email_sender_name', 'Project Management System', 'notification', 'string');

        // User Settings
        $this->createSetting('default_role', 'user', 'user', 'string');
        $this->createSetting('allow_registration', '0', 'user', 'boolean');
        $this->createSetting('account_approval', '1', 'user', 'boolean');
        $this->createSetting('password_min_length', '8', 'user', 'integer');
        $this->createSetting('password_requires_letters', '1', 'user', 'boolean');
        $this->createSetting('password_requires_numbers', '1', 'user', 'boolean');
        $this->createSetting('password_requires_symbols', '0', 'user', 'boolean');
        $this->createSetting('user_avatar_max_size', '2', 'user', 'integer');
        $this->createSetting('inactive_user_days', '90', 'user', 'integer');
    }

    /**
     * Create a setting if it doesn't exist
     */
    private function createSetting($key, $value, $group, $type)
    {
        Setting::firstOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type
            ]
        );
    }
}
