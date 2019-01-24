<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class sellingSystemLog
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
        log::info([$request->header(), $request->getMethod(), $request->getRequestUri(), $request->all(), $response->getStatusCode(), $response->getContent()]);
        return $response;
    }
}
