<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\AttackLog;


class MassRequestMiddleware
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

        // MASS REQUEST PROTECTION 
        if(env('MASS_REQUEST_PROTECTION')){ // (config('autnhive.mass_request_protection')) {
            if($request->session()){
                if ($request->session()->get('last_session_request') > time() - env('MASS_REQUEST_PROTECTION_THRESHOLD_TIME')) {
                    $this->registerAttack("MASS REQUEST");
                   $fullURL=url()->full();
					if(!strpos($fullURL, 'api')){
						abort(403, "Mass Request Attack");
					}else{
						return response()->json(['data' =>"Mass Request Attack"], 403);
					}
                }
                $request->session()->put('last_session_request',time());
            }else{
                $request->session()->put('last_session_request',time());
            }

        }
         return $next($request);
    }
}
