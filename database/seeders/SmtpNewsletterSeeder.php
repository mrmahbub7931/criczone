<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SmtpNewsletterSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── SMTP ──────────────────────────────────────────────────────────
            ['key' => 'smtp_host',          'value' => '',      'group' => 'smtp'],
            ['key' => 'smtp_port',          'value' => '587',   'group' => 'smtp'],
            ['key' => 'smtp_username',      'value' => '',      'group' => 'smtp'],
            ['key' => 'smtp_password',      'value' => '',      'group' => 'smtp'],
            ['key' => 'smtp_encryption',    'value' => 'tls',   'group' => 'smtp'],
            ['key' => 'smtp_from_email',    'value' => '',      'group' => 'smtp'],
            ['key' => 'smtp_from_name',     'value' => 'CricZone', 'group' => 'smtp'],

            // ── Newsletter ────────────────────────────────────────────────────
            ['key' => 'newsletter_enabled',         'value' => '1',            'group' => 'newsletter'],
            ['key' => 'newsletter_subject_prefix',  'value' => '[CricZone]',   'group' => 'newsletter'],
            ['key' => 'newsletter_welcome_subject', 'value' => 'Welcome to CricZone Newsletter!', 'group' => 'newsletter'],
            ['key' => 'newsletter_welcome_body',    'value' => "Hi there,\n\nThank you for subscribing to CricZone! You'll now receive the latest cricket news, match updates, and exclusive content straight to your inbox.\n\nStay tuned!\n\nThe CricZone Team", 'group' => 'newsletter'],
        ];

        foreach ($settings as $s) {
            Setting::firstOrCreate(
                ['key' => $s['key']],
                ['value' => $s['value'], 'group' => $s['group']]
            );
        }
    }
}
