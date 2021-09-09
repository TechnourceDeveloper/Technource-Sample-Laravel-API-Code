<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Auth;

class UserActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
      if ($request->user()->is_active == '0' && $request->user()->deactivated_by == '1') {
        $request->user()->tokens()->delete();
        throw new HttpResponseException(response()->json(['message' => "Your account is inactive please contact administrator.",'response'=>401], 401));
     }
        return $next($request);
    }
}
