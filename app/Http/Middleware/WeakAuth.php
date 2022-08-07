<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WeakAuth
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! isset($request->_p) || ! password_verify_weak_auth($request->_p)) {
            return response('unauthorized', 401);
        }

        return $next($request);
    }
}
