<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class Userauth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

		if(!Session::get('gpro_user') && !Session::get('gpro_exhibitor')){
			
			$request->session()->flash('gpro_error','Please first login');
			return redirect('/login');
		}
		
        return $next($request);
    }
}
