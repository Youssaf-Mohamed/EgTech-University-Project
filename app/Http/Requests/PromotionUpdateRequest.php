<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionUpdateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'promotion_amount' => 'sometimes|required|numeric|min:0',
            'promotion_priority' => 'sometimes|required|integer|min:0',
            'duration' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|required|in:active,inactive',
        ];
    }
}
