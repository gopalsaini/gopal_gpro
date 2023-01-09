<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Ranium\SeedOnce\Traits\SeedOnce;

class Roles extends Seeder
{
    use SeedOnce;
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(array(
            array(
            'name' => 'Admin',
			'guard_name' => 'web',
			)
        
        ));
    }
}
