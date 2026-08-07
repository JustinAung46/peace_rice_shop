<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ShopSettingsSeeder extends Seeder
{
    /**
     * Seed the settings table with Peace Rice Shop identity values.
     *
     * Uses updateOrCreate so it is safe to run multiple times — it will
     * never overwrite a value that an admin has already customised.
     * Run with: php artisan db:seed --class=ShopSettingsSeeder
     */
    public function run(): void
    {
        $defaults = [
            'shop_name'     => 'ငြိမ်းချမ်း',
            'shop_subtitle' => 'ဆန်ရောင်းဝယ်ရေး',
            'shop_address'  => 'ရပ်ကွက်(၉) ဘူတာလမ်း လားရှိုးမြို့',
            'shop_phone'    => '09-788024237, 09-5370682',
            'shop_footer1'  => 'Thank You, Please Come Again',
            'shop_footer2'  => 'Powered by Peace POS',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(
                ['key'   => $key],
                ['value' => $value]
            );
        }

        $this->command->info('✅ Shop settings seeded successfully.');
    }
}
