<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ranium\SeedOnce\Traits\SeedOnce;

class Designation extends Seeder
{
    use SeedOnce;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $designation = [
            ['designations' => 'Super admin'],
            ['designations' => 'Customer support'],
            ['designations' => 'Shipment team'],
            ['designations' => 'Opertaion person']
        ];
		
		\App\Models\Designation::insert($designation);
    }
}
