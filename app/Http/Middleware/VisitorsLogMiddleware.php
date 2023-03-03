<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Log;

use Closure;
use Auth;

use App\Traits\VisitorLog;

class VisitorsLogMiddleware
{
    use VisitorLog;

    private $visitorInfoId;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if(config('security.RECORD_VISITOR_REQUEST_LOG')){
            $visitorInfoId = $this->registerVisitorLog($request);
            $request->attributes->add(["visitor_info_id" => $visitorInfoId]);
        }
        return $next($request);
    }
    public function terminate($request, $response){
        if(config('security.RECORD_VISITOR_REQUEST_LOG')){
            $this->updateVisitorLog($request->get('visitor_info_id'), $response->getStatusCode());
        }
    }    
}
