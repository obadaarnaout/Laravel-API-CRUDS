<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PlayerSkillsResource;

class PlayerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($request->isMethod('put'))
        {
            $info = ['id' => $this->id];
            if (!empty($request->name)) {
                $info['name'] = $this->name;
            }
            if (!empty($request->position)) {
                $info['position'] = $this->position;
            }
            if (!empty($request->get('selectedSkills'))) {
                $info['playerSkills'] =  $this->skills()->whereIn('skill', $request->get('selectedSkills'))->get();
            }
            return $info;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'playerSkills' => PlayerSkillsResource::collection($this->skills)
        ];
    }
}
