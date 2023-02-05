<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePlayerRequest extends FormRequest
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
            'name' => 'required|string|unique:players',
            'position' => 'required|string|in:defender,midfielder,forward',
            'playerSkills' => 'required|array',
            'playerSkills.*.skill' => 'required|string|in:defense,attack,speed,strength,stamina',
            'playerSkills.*.value' => 'integer|max:127|min:-128',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!empty($this->playerSkills)) {
                $duplicated = [];
                foreach ($this->playerSkills as $key => $value) {
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
        });
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()->first()
        ],422));
    }
}
