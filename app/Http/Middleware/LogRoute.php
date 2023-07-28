<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class LogRoute
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

		$response = $next($request);

        $log = [
            'URI' => $request->getUri(),
            'METHOD' => $request->getMethod(),
            'REQUEST_BODY' => $request->all(),
            'Header_BODY' => $request->all(),
            'RESPONSE' => $response->getContent()
        ];

        config(['logging.channels.ErrorLog.path' => storage_path('logs/ErrorLog/'.time().'.log')]);
	    \Log::channel('ErrorLog')->info(json_encode($log));
    
        return $response;
    }
}
