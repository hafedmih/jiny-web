<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use Jenssegers\Agent\Agent;

use App\Traits\AttackLog;

class FakeBotMiddleware
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

         // FAKE BOT PROTECTION 
        if (env('FAKE_BOT_PROTECTION')){ // (config('autnhive.fake_bot_protection')) {
            $hostname = $request->getSchemeAndHttpHost();

            if (strpos(strtolower($useragentDetails), "googlebot") !== false) {
                if (strpos($hostname, "googlebot.com") !== false OR strpos($hostname, "google.com") !== false) {

                } else {
                    $this->registerAttack("FAKE BOT");
                    $fullURL=url()->full();
					if(!strpos($fullURL, 'api')){
						abort(403, "Fake bot Attack");
					}else{
						return response()->json(['data' =>"Fake bot Attack"], 403);
					}
                }
            }
            if (strpos(strtolower($useragentDetails), "bingbot") !== false) {
                if (strpos($hostname, "search.msn.com") !== false) {
                } else {
                    $this->registerAttack("FAKE BOT");
                    $fullURL=url()->full();
					if(!strpos($fullURL, 'api')){
						abort(403, "Fake bot Attack");
					}else{
						return response()->json(['data' =>"Fake bot Attack"], 403);
					}
                }
            }
            if (strpos(strtolower($useragentDetails), "yahoo! slurp") !== false) {
                if (strpos($hostname, "yahoo.com") !== false OR strpos($hostname, "crawl.yahoo.net") OR strpos($hostname, "yandex.com") !== false) {
                } else {
                    $this->registerAttack("FAKE BOT");
                    $fullURL=url()->full();
					if(!strpos($fullURL, 'api')){
						abort(403, "Fake bot Attack");
					}else{
						return response()->json(['data' =>"Fake bot Attack"], 403);
					};
                }
            }

        }
         return $next($request);
    }
}
