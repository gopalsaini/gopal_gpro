<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ranium\SeedOnce\Traits\SeedOnce;
use DB;
class hasPermission extends Seeder
{
    use SeedOnce;
	
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		$permissions = [
            ['permission_id' => 1, 'role_id' => 1],
            ['permission_id' => 2, 'role_id' => 1],
            ['permission_id' => 3, 'role_id' => 1],
            ['permission_id' => 4, 'role_id' => 1],
            ['permission_id' => 5, 'role_id' => 1],
        ];
		
		DB::table('role_has_permissions')->insert($permissions);
    }
}
