<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use Torann\GeoIP\Support\HttpClient;
use Torann\GeoIP\Services\AbstractService;

use Jenssegers\Agent\Agent;

use App\Traits\AttackLog;

use App\TrustedProxy;
// use App\AttackLogs;
// use App\AttackCount;

class BadBotMiddleware
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
        date_default_timezone_set('Asia/Dhaka');
        $data = date('Y-m-d');
        $time = date('h-A');
        file_put_contents("info.log",$time);
        Log::useFiles(base_path() . '/log/'.$data.'/'.$time.'-'.'info.log', 'info');
        Log::info('Do log this another PATH');

        // BAD BOT PROTECTION 
        if (config('security.BAD_BOT_PROTECTION')){ 
            $useragent = new Agent();
            $useragentDetails = $useragent->getUserAgent();    
            if ($useragent->isRobot()) {
                $this->registerAttack($request, "BAD_BOT_PROTECTION",false);                
                $fullURL=url()->full();
                if(!strpos($fullURL, 'api')){
                    abort(403, "Bad bot Attack");
                }else{
                    return response()->json(['data' =>"bad bot Attack"], 403);
                }
            }
        }
        return $next($request);
    }
    public function terminate($request, $response)
    {
        abort(403, "Bad bot Attack");
    }
}
