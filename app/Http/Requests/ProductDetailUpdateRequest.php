<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductDetailUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->vendors()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'material' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

}
