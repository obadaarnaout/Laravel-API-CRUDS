<?php

namespace App\Http\Middleware;

use App\Models\Player;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class FetchPlayerMiddleware
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
        $player_id = $request->id;
        $player = Player::find( $player_id);
        if(!$player){
            return response()->json(['message' => __('validation.player_not_found')], 404);
        }

        $request->request->add(['player' => $player]);
        return $next($request);
    }
}
