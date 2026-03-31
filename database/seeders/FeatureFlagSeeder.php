<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    public function run(): void
    {
        $flags = [
            // Payments
            ['key' => 'payments_enabled', 'name' => 'Payments', 'description' => 'Enable/disable all payment functionality', 'category' => 'payments', 'enabled' => true],
            ['key' => 'flutterwave_enabled', 'name' => 'Flutterwave', 'description' => 'Enable Flutterwave payment provider', 'category' => 'payments', 'enabled' => true],

            // Proctoring
            ['key' => 'proctoring_enabled', 'name' => 'Proctoring', 'description' => 'Allow assessment creators to enable proctoring', 'category' => 'proctoring', 'enabled' => true],
            ['key' => 'webcam_recording', 'name' => 'Webcam Recording', 'description' => 'Enable webcam recording via Cloudinary during tests', 'category' => 'proctoring', 'enabled' => true],
            ['key' => 'auto_end_on_leave', 'name' => 'Auto-End on Tab Switch', 'description' => 'Allow auto-end when candidates switch tabs', 'category' => 'proctoring', 'enabled' => true],

            // Assessment Features
            ['key' => 'advanced_question_types', 'name' => 'Advanced Question Types', 'description' => 'Enable psychometric, reasoning & interactive question types', 'category' => 'assessments', 'enabled' => false],
            ['key' => 'pattern_builder', 'name' => 'Pattern Builder', 'description' => 'Visual pattern/shape builder for question creation', 'category' => 'assessments', 'enabled' => false],
            ['key' => 'question_navigation', 'name' => 'Question Navigation Panel', 'description' => 'Show question navigation sidebar during tests', 'category' => 'assessments', 'enabled' => true],

            // Communication
            ['key' => 'email_notifications', 'name' => 'Email Notifications', 'description' => 'Send email notifications to users', 'category' => 'communication', 'enabled' => true],
            ['key' => 'send_answers_to_taker', 'name' => 'Candidate Answer Reports', 'description' => 'Allow sending detailed answer PDFs to candidates', 'category' => 'communication', 'enabled' => false],

            // Auth
            ['key' => 'google_oauth', 'name' => 'Google OAuth', 'description' => 'Enable Google sign-in', 'category' => 'auth', 'enabled' => true],
            ['key' => 'registration_enabled', 'name' => 'User Registration', 'description' => 'Allow new user registrations', 'category' => 'auth', 'enabled' => true],

            // Platform
            ['key' => 'maintenance_mode', 'name' => 'Maintenance Mode', 'description' => 'Put platform into maintenance mode', 'category' => 'platform', 'enabled' => false],
            ['key' => 'analytics_enabled', 'name' => 'Analytics Dashboard', 'description' => 'Enable admin analytics dashboard', 'category' => 'platform', 'enabled' => true],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::updateOrCreate(['key' => $flag['key']], $flag);
        }
    }
}
