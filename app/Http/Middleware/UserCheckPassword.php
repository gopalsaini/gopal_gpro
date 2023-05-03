<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class UserCheckPassword
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
        
		if(Session::has('gpro_result') && Session::get('gpro_result')['system_generated_password'] == '1'){
			
			
			$request->session()->flash('gpro_error','Please update your password');
			return redirect('change-password');

		}elseif(Session::has('gpro_result_exhibitor') &&  Session::get('gpro_result_exhibitor')['system_generated_password'] == '1' && Session::get('gpro_result_exhibitor')['system_generated_password'] == '1'){
			
			
			$request->session()->flash('gpro_error','Please update your password');
			return redirect('change-password');
		}
		
        return $next($request);
    }
}
