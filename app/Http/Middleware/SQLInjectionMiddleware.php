<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use App\Traits\AttackLog;

class SQLInjectionMiddleware
{
    use AttackLog;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $content = $request->all();
        if(!empty($content['query'])){
            if(!preg_match('(^([a-zA-z0-9@_ ]+)(\d+)?$)', $content['query'])){
                $this->registerAttack($request, "SQL INJECTION", false);
                
				$fullURL=url()->full();
				if(!strpos($fullURL, 'api')){
					abort(400, "SQL Injection Thread Identified");
				}else{
					return response()->json(['data' =>"SnQL Injection Thread Identified"], 400);
				}
            }
            if(strlen($content['query'])>20){
                $this->registerAttack($request, "SQL INJECTION", false);
                $fullURL=url()->full();
				if(!strpos($fullURL, 'api')){
					abort(400, "SQL Injection Thread Identified");
				}else{
					return response()->json(['data' =>"SQL Injection Thread Identified"], 400);
				}
            }
        }

        return $next($request);
    }
}
