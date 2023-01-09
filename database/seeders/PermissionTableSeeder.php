<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Seeder;
use DB;
use Ranium\SeedOnce\Traits\SeedOnce;

class PermissionTableSeeder extends Seeder
{
    use SeedOnce;
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert(array(
            array(
				'name' => 'Roles',
				'url' => '#',
				'icon' => 'fa fa-user',
				'parent_id' => '0',
				'guard_name' => 'web'
			),
             array(
				'name' => 'role-create',
				'url' => 'roles',
				'icon' => '#',
				'parent_id' => '1',
				'guard_name' => 'web',
			),
             array(
				'name' => 'Categories',
				'url' => '#',
				'icon' => 'fa fa-list',
				'parent_id' => '0',
				'guard_name' => 'web',
			),
             array(
				'name' => 'Add',
				'url' => 'admin/category/add',
				'icon' => 'fa fa-circle-o',
				'parent_id' => '3',
				'guard_name' => 'web',
			),
             array(
				'name' => 'List',
				'url' => 'admin/category/list',
				'icon' => 'fa fa-circle-o',
				'parent_id' => '3',
				'guard_name' => 'web',
			)
        ));
    }
}