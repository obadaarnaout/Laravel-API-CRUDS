<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdatePlayerRequest extends FormRequest
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
            'id' => 'required|exists:players,id',
            'name' => 'string',
            'position' => 'string|in:defender,midfielder,forward',
            'playerSkills' => 'array',
            'playerSkills.*.skill' => 'required|string|in:defense,attack,speed,strength,stamina',
            'playerSkills.*.value' => 'required|integer|max:127|min:-128',
        ];
    }
    protected function prepareForValidation() 
    {
        $this->merge(['id' => $this->route('id')]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $inputs = $this->all();
            if (empty($inputs['name']) && empty($inputs['position']) && empty($inputs['playerSkills'])) {
                throw new HttpResponseException(response()->json([
                    'message' => __('validation.empty_request')
                ],422));
            }

            if (!empty($inputs['playerSkills'])) {
                $duplicated = [];
                foreach ($inputs['playerSkills'] as $key => $value) {
                    if (empty($value['skill'])) {
                        throw new HttpResponseException(response()->json([
                            'message' => __('validation.required_field',['attribute' => 'skill'])
                        ],422));
                    }
                    $name = $value['skill'];
                    if (isset($duplicated[$name])) {
                        $validator->errors()->add('body', __('validation.player_details_twice'));
                    }
                    else{
                        $duplicated[$name] = 1;
                    }
                }
            }

            $player_id = $this->id;
            
            if (!empty($this->name)) {
                $nameCount = \App\Models\Player::where('id','!=',$player_id)->where('name',$this->name)->count();
                
                if ($nameCount > 0) {
                    throw new HttpResponseException(response()->json([
                        'message' => __('validation.name_taken')
                    ],422));
                }
            }
            $player = \App\Models\Player::find( $player_id);

            $this->request->add(['player' => $player]);
        });
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()->first()
        ],422));
    }
}
