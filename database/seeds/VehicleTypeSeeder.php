<?php

use App\VehicleType;
use App\VehicleTypeTranslation;

use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicle_type = \App\VehicleType::create([
            'vehicle_type_order' => 1,
        ]);

        VehicleTypeTranslation::create([
            'vehicle_type_id'=>$vehicle_type->id,
            'name'=>'Car',
            'locale'=>'en',
        ]);

        VehicleTypeTranslation::create([
            'vehicle_type_id'=>$vehicle_type->id,
            'name'=>'سيارة',
            'locale'=>'ar',
        ]);
    }
}
