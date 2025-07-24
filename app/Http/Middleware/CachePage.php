<?php

namespace App\Http\Middleware;

use Cache;
use Closure;

class CachePage
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
       // $key = $request->fullUrl();

        //if (env('APP_ENV', 'local') == 'local')
         // return $next($request);

       // if (Cache::has($key))
           // return response(Cache::get($key));

        $response = $next($request);

       // $cachingTime = 30; // 30 minutes
        //Cache::put($key, $response->getContent(), $cachingTime);

        $response->header('Cache-Control', 'public, max-age=14400');
        // $response->header('Cache-Control', 'public, max-age=3600');

        return $response;
    }
}
