<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamResource;
use App\Models\Player;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function search(\App\Http\Requests\TeamSelectionRequest $request)
    {
        $result = [];

        $inputs = $request->all();
        foreach ($inputs as $key => $value) {
            $position = $value['position'];
            $skill = $value['mainSkill'];
            $limit = $value['numberOfPlayers'];
            
            $players = Player::join('player_skills','player_skills.player_id','=','players.id')->where('players.position',$position)->where('player_skills.skill',$skill)->select('players.*')->orderBy('player_skills.value','desc')->limit($limit)->get();
            $result[] = TeamResource::collection($players);

            if($players->count() < $limit){
                $limit -= $players->count();
                $players = Player::join('player_skills','player_skills.player_id','=','players.id')->where('players.position',$position)->where('player_skills.skill','!=',$skill)->select('players.*')->orderBy('player_skills.value','desc')->limit($limit)->get();
                $result[] = TeamResource::collection($players);
            }
        }

        $response = [];
        foreach ($result as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $response[] = $value2;
            }
            
        }
        return $response;

    }
}
