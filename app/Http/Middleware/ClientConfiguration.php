<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use Log;

class ClientConfiguration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        dd('ClientConfiguration executado', $request->getHost());
        $root = app('request')->root();
        $root = str_replace(['https://', 'http://', 'www.'], '', $root);


        if (!Config::has("clients.$root")) {
            $root = '';
            //$root = 'site.localhost';
        }

        Log::info($root);

        if ($root != session()->get('client')) {
            session()->put('client', $root);
        }

        return $next($request);
    }
}
