<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
		
		Schema::defaultStringLength(191); 

		View::composer('layouts.app', function($view){

        $informations = \App\Models\Information::where('status', '1')->get();

			  return $view->with(['informations' => $informations]);
		});
    }
}
