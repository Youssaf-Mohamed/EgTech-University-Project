<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'promotion_amount' => 'required|numeric|min:0',
            'promotion_priority' => 'required|integer|min:0',
            'duration' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ];
    }
}
