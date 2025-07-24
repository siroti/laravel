<?php

namespace App\Http\Middleware;

use App\Statistics\Facades\PropertyView;
use Closure;

class Statistics{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        PropertyView::setViews($request->cod);

        return $next($request);
    }
}
