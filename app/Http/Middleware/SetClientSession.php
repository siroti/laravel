<?php
namespace App\Http\Middleware;

use Closure;

class SetClientSession
{
    public function handle($request, Closure $next)
    {
        
        $host = $request->getHost(); // Ex: 'novosite.localhost'
        session(['client' => $host]);
        return $next($request);
    }
}