<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Super Admin
        Admin::firstOrCreate(
            ['email' => 'admin@quizly.com'],
            [
                'name' => 'Super Admin',
                'password' => 'QuizlyAdmin@2026!',
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // Seed Subscription Plan
        SubscriptionPlan::firstOrCreate(
            ['name' => 'Professional'],
            [
                'monthly_price' => 15000.00, // NGN - ₦15,000/month
                'annual_discount_percent' => 15.00,
                'features' => [
                    'Unlimited Assessments',
                    'Unlimited Candidates',
                    'Advanced Analytics Dashboard',
                    'Export Results to Excel',
                    'Custom Branding',
                    'Priority Support',
                    'Anti-Fraud Detection',
                    'Proctored Testing',
                ],
                'is_active' => true,
            ]
        );

        // Seed Default Settings
        $settings = [
            ['key' => 'platform_name', 'value' => 'Quizly', 'type' => 'string', 'description' => 'Platform display name'],
            ['key' => 'annual_discount_percent', 'value' => '15', 'type' => 'float', 'description' => 'Annual subscription discount percentage'],
            ['key' => 'fuzzy_match_threshold', 'value' => '85', 'type' => 'int', 'description' => 'Name similarity threshold for fraud detection (%)'],
            ['key' => 'max_email_batch_size', 'value' => '50', 'type' => 'int', 'description' => 'Max emails per batch (Migadu limits)'],
            ['key' => 'email_batch_delay_seconds', 'value' => '60', 'type' => 'int', 'description' => 'Delay between email batches'],
            ['key' => 'rate_limit_api', 'value' => '100', 'type' => 'int', 'description' => 'API requests per minute'],
            ['key' => 'rate_limit_auth', 'value' => '5', 'type' => 'int', 'description' => 'Auth requests per minute'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
