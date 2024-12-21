<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use App\Traits\AttackLog;

use Torann\GeoIP\Support\HttpClient;
use Torann\GeoIP\Services\AbstractService;

class TorMiddleware
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
         // TOR PROTECTION 
        if (env('TOR_PROTECTION')){ // (config('autnhive.tor_protection')) {
            $ipAddress = geoip()->getClientIP();
            $dnsbl_lookup = array(  "tor.dan.me.uk", "tor.dnsbl.sectoor.de");
            $reverse_ip   = implode(".", array_reverse(explode(".", $ipAddress)));
            foreach ($dnsbl_lookup as $host) {
                if (checkdnsrr($reverse_ip . "." . $host . ".", "A")) {

                    $this->registerAttack("TOR BROWSER");
                     $fullURL=url()->full();
					if(!strpos($fullURL, 'api')){
						abort(403, "TOR Attack");
					}else{
						return response()->json(['data' =>"Tor Attack"], 403);
					}
                }
            }
             
        }
         return $next($request);
    }
}
