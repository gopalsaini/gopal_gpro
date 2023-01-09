<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;
use DB;

class ConfigureCompanyDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){

		$company = $_SERVER['SERVER_NAME'];

        if(Session::has('client_database') && (Session::has('client_website') && Session::get('client_website')==$company)){

            $connection = Session::get('client_database');

        }else{

            $connection = $this->getConnectionForCompany($company);

            Session::put('client_database',$connection);
            Session::put('client_website',$company);

        }

        config()->set('database.connections.client.database', $connection); // swap default database connection

        DB::purge(); // purge existing connection, db will reconnect with provided credentials on first db request.

        return $next($request);
    }


    protected function getConnectionForCompany(string $company): string{

        return '5ferns_img';
    }
}
