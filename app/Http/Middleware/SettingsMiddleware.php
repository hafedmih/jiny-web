<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\taxi\Settings;
use App\Models\taxi\Requests\Request as RequestModel;

class SettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $setting = Settings::where('status',1)->get();
        $data = [];
        foreach ($setting as $value) {
            $data[$value->name] = $value->image ? $value->image : $value->value;
        }
        $request_count = RequestModel::whereNull('driver_id')->where('is_cancelled',0)->where('is_driver_started',0)->count();
        $data['request_live_count'] = $request_count; 
        session(['data' => $data]);

        return $next($request);
    }
}
