<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Ranium\SeedOnce\Traits\SeedOnce;

class model_has_roles extends Seeder
{
    use SeedOnce;
	
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('model_has_roles')->insert(array(
            array(
            'role_id' => '1',
            'model_type' => 'App\Models\User',
            'model_id' => '1',
			)
        
        ));
    }
}
