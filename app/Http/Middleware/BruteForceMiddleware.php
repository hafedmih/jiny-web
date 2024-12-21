<?php

namespace App\Http\Middleware;


use Closure;
use Carbon\Carbon;

use Torann\GeoIP\Support\HttpClient;
use Torann\GeoIP\Services\AbstractService;

use App\Traits\AttackLog as AttackLog;

use Jenssegers\Agent\Agent;

use App\TrustedProxy;
// use App\BruteForceIp;
// use App\BruteForceEmail;
use App\AttackLogs;

class BruteForceMiddleware
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
        $useragent = new Agent();
        $useragentDetails = $useragent->getUserAgent();
        $bruteForceThreshold = config('security.BRUTE_FORCE_PROTECTION_BAN_THRESHOLD', 10);

        if(config('security.BRUTE_FORCE_PROTECTION')){

            $ipAddress = geoip()->getClientIP();
            $ip = geoip($ipAddress);
            // $bruteForceIp = BruteForceIp::where('ip_address',$ipAddress)->whereDate('created_at',Carbon::today())->where('type','BRUTE FORCE')->count();
            $bruteForceIp = AttackLogs::where('ip_address',$ipAddress)->whereDate('created_at',Carbon::today())->where('type','BRUTE FORCE')->count();
            if($bruteForceIp > $bruteForceThreshold){
                $this->registerAttack($request, "BRUTE FORCE");
                $fullURL=url()->full();
				if(!strpos($fullURL, 'api')){
					abort(403, "Brute Force Attack");
				}else{
					return response()->json(['data' =>"Brute Force Attack"], 403);
				}
                
            }
			if(!is_null($request->input('email')) && $request->input('email') !=''){

				// $bruteForceEmail = BruteForceEmail::where('email',$request->input('email'))->whereDate('created_at',Carbon::today())->where('type','BRUTE FORCE')->count();   
				$bruteForceEmail = AttackLogs::where('email',$request->input('email'))->whereDate('created_at',Carbon::today())->where('type','BRUTE FORCE')->count();   
				if($bruteForceEmail > $bruteForceThreshold){
					$this->registerAttack($request, "BRUTE FORCE");
					 $fullURL=url()->full();
					if(!strpos($fullURL, 'api')){
						abort(403, "Brute Force Attack");
					}else{
						return response()->json(['data' =>"Brute Force Attack"], 403);
					}
				}
			}

        }
         return $next($request);
    }
}
