<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use App\Traits\AttackLog;

use Torann\GeoIP\Support\HttpClient;
use Torann\GeoIP\Services\AbstractService;

use Jenssegers\Agent\Agent;

// use App\Traits\AttackLog;

use App\TrustedProxy;
// use App\AttackLogs;
// use App\AttackCount;

class ProxyMiddleware
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

        // PROXY PROTECTION 
        if (env('PROXY_PROTECTION')){ // (config('autnhive.proxy_protection')) {

            //  Method 1
            $proxy_headers = array(
                'HTTP_VIA',
                'HTTP_X_FORWARDED_FOR',  // Removed for AWS issues
                'HTTP_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_FORWARDED',
                'HTTP_CLIENT_IP',
                'HTTP_FORWARDED_FOR_IP',
                'VIA',
                'X_FORWARDED_FOR',
                'FORWARDED_FOR',
                'X_FORWARDED',
                'FORWARDED',
                'CLIENT_IP',
                'FORWARDED_FOR_IP',
                'HTTP_PROXY_CONNECTION'
                // ,'HTTP_ACCEPT'
            );
            $requestVariable = $request->server();

            foreach ($proxy_headers as $x) {
                if (isset($requestVariable[$x])) {
                    $this->registerAttack("PROXY");
                    $fullURL=url()->full();
					if(!strpos($fullURL, 'api')){
						abort(400, "Proxy Thread Identified");
					}else{
						return response()->json(['data' =>"Proxy Thread Identified"], 400);
					}
                } 
            }

            // Method 2
            /*$ports = array(
                8080,
                80,
                81,
                1080,
                6588,
                8000,
                3128,
                553,
                554,
                4480
            );
            $requestVariable = $request->server();
            dd($requestVariable);
            foreach ($ports as $port) {
                if (@fsockopen($ip, $port, $errno, $errstr, 30)) {

                    $this->registerAttack("PROXY");
                    abort(403);
                } 
            }*/
        }   

        $response = $next($request);
        return $response;
    }
}
