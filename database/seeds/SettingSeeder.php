<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'meta_key' => 'is_make_search',
            'meta_value' => 1,
            'type' => 1,
        ]);

        Setting::create([
            'meta_key' => 'is_model_search',
            'meta_value' => 1,
            'type' => 1,
        ]);

        Setting::create([
            'meta_key' => 'is_category_search',
            'meta_value' => 1,
            'type' => 1,
        ]);
        Setting::create([
            'meta_key' => 'is_insurance_search',
            'meta_value' => 1,
            'type' => 1,
        ]);

        Setting::create([
            'meta_key' => 'is_dealer_search',
            'meta_value' => 1,
            'type' => 1,
        ]);

        Setting::create([
            'meta_key' => 'default_radius_km',
            'meta_value' => 25,
            'type' => 2,
            'class_value' => 'integer',
        ]);
    }
}
