<?php

use App\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::create([
            'name' => 'English',
            'is_rtl' => 0,
            'language_code' => 'en',
            'status' => 'Active'
        ]);
        Language::create([
            'name' => 'Arabic',
            'is_rtl' => 1,
            'language_code' => 'ar',
            'status' => 'Active'
        ]);
    }
}
