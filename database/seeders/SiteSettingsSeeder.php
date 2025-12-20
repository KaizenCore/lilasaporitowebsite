<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'business_name',
                'value' => 'FrizzBoss Art Classes',
                'type' => 'text',
            ],
            [
                'key' => 'business_tagline',
                'value' => 'Where Ms. Frizzle meets Bob Ross',
                'type' => 'text',
            ],
            [
                'key' => 'bio',
                'value' => 'Welcome to FrizzBoss! I\'m Lila, and I love teaching art and helping people discover their creativity.',
                'type' => 'text',
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/frizzboss',
                'type' => 'url',
            ],
            [
                'key' => 'contact_email',
                'value' => 'hello@frizzboss.ca',
                'type' => 'text',
            ],
            [
                'key' => 'default_location',
                'value' => 'Art Studio, Main Street',
                'type' => 'text',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }

        $this->command->info('Site settings seeded successfully!');
    }
}
