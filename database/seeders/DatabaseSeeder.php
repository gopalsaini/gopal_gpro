<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
     
	/**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionTableSeeder::class);
		$this->call(Roles::class);
		$this->call(hasPermission::class);
		$this->call(model_has_roles::class);
		$this->call(Information::class);
		$this->call(Designation::class);
		$this->call(Menu::class);
		$this->call(User_role::class);
		$this->call(Setting::class);
		$this->call(Currency::class);
		\Artisan::call('seedonce:mark-seeded');
    }
}
