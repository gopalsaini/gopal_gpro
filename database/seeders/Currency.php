<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

use Ranium\SeedOnce\Traits\SeedOnce;

class Currency extends Seeder
{
    
    use SeedOnce;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currency_values')->insert(array(
            array(
				'name' => '₹',
				'currency_code' => 'INR',
				'image' => asset('public/uploads/flag/INR.png'),
				'first_icon' => 'Rs.',
				'second_icon' => '',
				'value' => '1',
			),
             array(
				'name' => 'USD',
				'currency_code' => 'USD',
				'image' => asset('public/uploads/flag/USD.png'),
				'first_icon' => '$',
				'second_icon' => 'USD',
				'value' => '0.0131528',
			),
             array(
				'name' => 'EUR',
				'currency_code' => 'EUR',
				'image' => asset('public/uploads/flag/EUR.png'),
				'first_icon' => '€',
				'second_icon' => 'EUR',
				'value' =>'0.0117067',
			),
             array(
				'name' => 'GBP',
				'currency_code' => 'GBP',
				'image' => asset('public/uploads/flag/GBP.png'),
				'first_icon' => '£',
				'second_icon' => 'GBP',
				'value' => '0.00993440',
             ),
            array(
				'name' => 'AUD',
				'currency_code' => 'AUD',
				'image' => asset('public/uploads/flag/AUD.png'),
				'first_icon' => '$',
				'second_icon' => 'AUD',
				'value' => '0.0184736',
			),
            array(
				'name' => 'AED',
				'currency_code' => 'AED',
				'image' => asset('uploads/flag/AED.png'),
				'first_icon' => 'AED',
				'second_icon' => '',
				'value' => '0.0482398',
			)
        ));
    }
}
