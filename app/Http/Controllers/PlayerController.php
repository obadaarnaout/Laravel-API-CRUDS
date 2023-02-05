<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW. 
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;
use App\Enums\PlayerPosition;
use App\Models\PlayerSkill;
use App\Models\Player;
use App\Http\Resources\PlayerResource;

class PlayerController extends Controller
{
    public function __construct()
    {
        $this->middleware('deletePlayer')->only('destroy');
        $this->middleware('fetchPlayer')->only(['destroy','show','update']);
    }
    public function index()
    {
        return PlayerResource::collection(Player::all());
    }

    public function show(\Illuminate\Http\Request $request)
    {
        return new PlayerResource($request->get('player'));
    }

    public function store(\App\Http\Requests\StorePlayerRequest $request)
    {
        $player = Player::create([
            'name' => $request->input('name'),
            'position' => PlayerPosition::getValue(strtoupper($request->input('position'))),
        ]);

        $playerSkills = $request->input('playerSkills');
        foreach ($playerSkills as $key => $value) {
            $skills = new PlayerSkill();
            $skills->skill = \App\Enums\PlayerSkill::getValue(strtoupper($value['skill']));
            if (!empty($value['value'])) {
                $skills->value = $value['value'];
            }
            $skills->player_id = $player->id;
            $skills->save();
        }
        return response()->json(new PlayerResource($player), 200);
    }

    public function update(\App\Http\Requests\UpdatePlayerRequest $request,$player_id)
    {
        $player = $request->get('player');

        $updateData = [];
        if (!empty($request->input('name'))) {
            $updateData['name'] = $request->input('name');
        }
        if (!empty($request->input('position'))) {
            $updateData['position'] = PlayerPosition::getValue(strtoupper($request->input('position')));
        }

        if (!empty($updateData)) {
            $player->update($updateData);
        }

        $playerSkills = $request->input('playerSkills');
        if (!empty($playerSkills)) {
            $selectedSkills = [];
            $player->skills()->delete();
            foreach ($playerSkills as $key => $value) {
                if (PlayerSkill::where('skill',$value['skill'])->where('player_id',$player->id)->count() > 0) {
                    $player->skills()->where('skill',$value['skill'])->update([
                        'value' => $value['value']
                    ]);
                }
                else{
                    $skills = new PlayerSkill();
                    $skills->skill = \App\Enums\PlayerSkill::getValue(strtoupper($value['skill']));
                    $skills->value = $value['value'];
                    $skills->player_id = $player->id;
                    $skills->save();
                }
                $selectedSkills[] = $value['skill'];
            }

            request()->request->add(['selectedSkills' => $selectedSkills]);
        }
        

        return new PlayerResource($player);
    }

    public function destroy(\Illuminate\Http\Request $request)
    {
        $player = $request->get('player')->delete();

        return response()->json([
            'message' => 'Player deleted successfully'
        ], 200);
    }
}
