<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DeletePlayerMiddleware
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
        $header = $request->header('Authorization', '');
        if (\Illuminate\Support\Str::startsWith($header, 'Bearer ')) {
            $token = \Illuminate\Support\Str::substr($header, 7);
            if ($token != 'SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE=') {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        }
        else{
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }
        return $next($request);
    }
}
