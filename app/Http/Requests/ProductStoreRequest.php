<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->is_active;
    }

    public function rules()
    {
        return [
            'product_name' => 'required|string|unique:products,product_name',
            'description' => 'nullable|string',
            'categories' => 'array|exists:categories,id',
            'details.*.size' => 'nullable|string',
            'details.*.color' => 'nullable|string',
            'details.*.price' => 'required|numeric|min:0',
            'details.*.discount' => 'nullable|numeric|min:0',
            'details.*.stock' => 'required|integer|min:0',
            'details.*.material' => 'nullable|string',
            'details.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
