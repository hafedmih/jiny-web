<?php

namespace App\Http\Middleware;

use Closure;
use RuntimeException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

use Carbon\Carbon;

use App\Traits\AttackLog;

class ThrottleMiddleware
{
    use AttackLog;

    /**
     * Amount of time (in minutes) the last request will be stored,
     *
     */
    protected $cacheForMinutes = 1;

    /**
     * The limit of concurrent requests the current user can run.
     *
     */
    protected $limit;

    /**
     * The current user's signature.
     *
     */
    protected $signature;

    /**
     * Prefix to be on the request signature.
     *
     */
    protected $prefix = 'concurrent:';

    public function handle($request, Closure $next, $limit = 60)
    {
        $this->limit = (int) $limit;
        $this->setRequestSignature($request);

        // var_dump(Cache::store('file')->get($this->signature));

        if ($this->limit <= Cache::store('file')->get($this->signature)) {
            $fullURL=url()->full();
            if(!strpos($fullURL, 'api')){
                abort(429, "Too Many Attempts.");
            }else{
                return response()->json(['data' =>"Too Many Attempts"], 429);
            }               
        }
        $this->increment();

        // request()->headers->set('X-RateLimit-Limit', $this->cacheForMinutes);
        // request()->headers->set('X-RateLimit-Limit', $this->getRemainingRequests($this->limit));
        // dd(request()->headers);

        return $next($request);
    }
    /**
     * Handle the outgoing response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return mixed
     */
    public function terminate($request, $response)
    {
        $this->decrement();
        return $response;
    }
    /**
     * Get the number of remaining concurrent requests the user can run.
     */
    protected function getRemainingRequests(int $limit): int
    {
        return max(0, $limit - Cache::store('file')->get($this->signature));
    }
    /**
     * Get headers to denote the current rate limits the user has.
     */
    protected function getHeaders(): array
    {
        return [
            'X-RateLimit-Limit' => $this->limit,
            'X-RateLimit-Remaining' => $this->getRemainingRequests($this->limit),
        ];
    }
    /**
     * Manually set the signature for the current request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string|null $signature
     * @return string
     */
    public function setRequestSignature($request, $signature = null)
    {
        if (!empty($this->signature)) {
            return $signature;
        }
        $signature = $this->prefix . sha1($signature ?? $this->resolveRequestSignature($request));
        $this->signature = $signature;
        return $signature;
    }
    /**
     * Resolve the request signature for the current requesting user.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     * @throws \RuntimeException
     */
    protected function resolveRequestSignature($request)
    {
        if (!empty($this->signature)) {
            return $this->signature;
        }
        if ($user = $request->user()) {
            return $user->getAuthIdentifier();
        }
        if ($route = $request->route()) {
            return $route->getDomain().'|'.$request->ip();
        }
        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }
    /**
     * Increment the count of currently running requests for the current user by 1.
     *
     * @return integer
     */
    protected function increment(): int
    {
        $value = 1;
        if (Cache::store('file')->has($this->signature)) {
            $value = Cache::store('file')->get($this->signature) + 1;
        }
        Cache::store('file')->put($this->signature, $value, $this->cacheForMinutes);
        return $value;
    }
    /**
     * Decrement the count of currently running requests for the current user by 1.
     */
    protected function decrement(): int
    {
        if (! Cache::store('file')->has($this->signature)) {
            return 0;
        }
        $value = Cache::store('file')->get($this->signature) - 1;
        if ($value === 0) {
            Cache::store('file')->forget($this->signature);
            return 0;
        }
        Cache::store('file')->put($this->signature, $value);
        return $value;
    }
}
