<?php

namespace App\Http\Requests;

use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TeamSelectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            '*.position' => 'required|string|in:defender,midfielder,forward',
            '*.mainSkill' => 'required|string|in:defense,attack,speed,strength,stamina',
            '*.numberOfPlayers' => 'required|integer|min:1'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $inputs = $this->all();
            if (empty($inputs)) {
                throw new HttpResponseException(response()->json([
                    'message' => __('validation.empty_request')
                ],422));
            }
            $duplicated = [];
            foreach ($inputs as $key => $value) {
                if (empty($value['position'])) {
                    throw new HttpResponseException(response()->json([
                        'message' => __('validation.required_field',['attribute' => 'position'])
                    ],422));
                }
                elseif (empty($value['mainSkill'])) {
                    throw new HttpResponseException(response()->json([
                        'message' => __('validation.required_field',['attribute' => 'mainSkill'])
                    ],422));
                }
                elseif (empty($value['numberOfPlayers'])) {
                    throw new HttpResponseException(response()->json([
                        'message' => __('validation.required_field',['attribute' => 'numberOfPlayers'])
                    ],422));
                }

                $name = $value['position'] . '-' . $value['mainSkill'];
                if (isset($duplicated[$name])) {
                    $validator->errors()->add('body', __('validation.player_details_twice'));
                }
                else{
                    $duplicated[$name] = 1;
                }

                $count = Player::where('position', $value['position'])->count();
                if($count < $value['numberOfPlayers']){
                    $validator->errors()->add('body', __('validation.insufficient_number_of_players',['input' => $value['position']]));
                }
            }
        });
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()->first()
        ],422));
    }
}
