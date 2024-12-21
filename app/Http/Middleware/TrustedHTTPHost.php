<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use App\Traits\AttackLog;

class TrustedHTTPHost
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
        /*$trusted_host = env('TRUSTED_HTTP_HOST', null);
        if(request()->getHttpHost() == 'www.'.$trusted_host){

        }else if(request()->getHttpHost() == $trusted_host){
        
        }else{
            $fullURL=url()->full();
            if(!strpos($fullURL, 'api')){
                // dd(request()->getHttpHost());
                request()->headers->set('Host', $trusted_host);
                abort(400, "Invalid Host");
            }else{
                return response()->json(['data' =>"Invalid host"], 400);
            }           
        }*/

        return $next($request);
    }
}
