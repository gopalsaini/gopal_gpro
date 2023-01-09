<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

use Ranium\SeedOnce\Traits\SeedOnce;

class Information extends Seeder
{
    
    use SeedOnce;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('informations')->insert(array(
            array(
				'title' => 'Terms & Condition',
				'description' => 'Coming soon'
			),
             array(
				'title' => 'Privacy Policy',
				'description' => 'Coming soon'
			),
             array(
				'title' => 'About Us',
				'description' => 'Coming soon'
			),
             array(
				'title' => 'Return and Refund policy',
				'description' => 'Coming soon'
             ),
            array(
				'title' => 'Shipping Policy',
				'description' => 'Coming soon'
			),
            array(
				'title' => 'Cancellation Policy',
				'description' => 'Coming soon'
			)
        ));
    }
}
