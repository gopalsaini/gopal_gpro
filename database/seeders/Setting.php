<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Setting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting=[
            [
                'type'=>'delivery_shipping_charge',
                'value'=>'65'
            ],
            [
                'type'=>'free_shipping',
                'value'=>'5000'
            ],
            [
                'type'=>'delivery_international_shipping_charge',
                'value'=>'5000'
            ],
        ];

        \App\Models\Setting::insert($setting);
    }
}
