<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use App\Traits\AttackLog;

class XssMiddleware
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
        // check XSS Clean - START
        // if (!in_array(strtolower($request->method()), ['put', 'post'])) {
        //    return $next($request);
        // }

        $input = $request->all();             
        foreach($input as $key => $row){            
            // $input[$key] = strip_tags($row);
            $input[$key] = $row;
            if(!is_array($row))
            {
                if($key != 'password' && $key != 'password_confirmation' ){
                    $fullURL=url()->full();
                    if(!strpos($fullURL, 'api')){
                        if(!preg_match('(^([a-zA-z0-9-:@_,#. ]+)(\d+)?$)', $input[$key]) && !is_null($input[$key]) && $input[$key] != ''){                    
                            $this->registerAttack($request, "XSS ATTACK", false);
                            abort(400, "XSS Attack");
                        }
                    }else{
                        if(!preg_match('(^([a-zA-z0-9-:@_. ]+)(\d+)?$)', $input[$key]) && !is_null($input[$key]) && $input[$key] != ''){                    
                            $this->registerAttack($request, "XSS ATTACK", false);
                            return response()->json(['data' =>"XSS ATTACK",'error'=>'true'], 400);
                        }
                    }
                }
            }
        }
        return $next($request);
    }
}
