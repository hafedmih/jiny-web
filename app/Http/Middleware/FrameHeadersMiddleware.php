<?php

namespace App\Http\Middleware;

use Closure;

class FrameHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		$response = $next($request);
		$response->header('X-Frame-Options', 'ALLOW FROM https://example.com/');	
         $response->header('X-Frame-Options','deny'); // Anti clickjacking	
         $response->header('X-Frame-Options','deny'); // Anti clickjacking
        $response->header('X-XSS-Protection', '1'); // Anti cross site scripting (XSS)
        $response->header('X-Content-Type-Options', 'nosniff'); // Reduce exposure to drive-by dl attacks
        // $response->header('Content-Security-Policy', 'default-src \'self\''); // Reduce risk of XSS, clickjacking, and other stuff
        $response->header('Content-Security-Policy', 'script-src \'self\' data: https: \'unsafe-inline\' \'unsafe-eval\'; object-src *; style-src \'self\' data: https: https: \'unsafe-eval\' \'unsafe-inline\'; img-src * \'self\' data: https:; font-src \'self\' data: https: \'unsafe-eval\'; connect-src *');
        // Don't cache stuff (we'll be updating the page frequently)
        $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Access-Control-Max-Age', '28800');
  //       return $next($request);
    }
}
