<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ranium\SeedOnce\Traits\SeedOnce;

class User extends Seeder
{
    use SeedOnce;
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
		
        $user = [
            ['name' => 'admin','email' => 'admin@gmail.com','reg_type'=>'admin','status'=>'1','user_type'=>'1','password'=>Hash::make('123456789')]
		];
		 
		\App\Models\User::insert($user);
    }
}
