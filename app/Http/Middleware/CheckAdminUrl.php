<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class CheckAdminUrl
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
		if(Session::has('insuredbeadminrexceptionurl')){

			$exception_uris=Session::get('insuredbeadminrexceptionurl');

			$uri = null;

			for ($i = 1; $i <= count($request->segments()); $i++) {
				$uri .= $request->segment($i) . '/';
				$result = rtrim($uri, '/');
				if (in_array($result, $exception_uris) == true) {
					return redirect('admin/dashboard')->with('5fernsadminerror','Permission denied');
				}
			}
		}
		
        return $next($request);
    }
}
