<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->vendors()->exists();
    }

    public function rules()
    {
        return [
            'product_name' => 'sometimes|required|string|unique:products,product_name,' . $this->route('product')->id,
            'description' => 'nullable|string',
            'categories' => 'array|exists:categories,id',
            'details.*.id' => 'nullable|exists:product_details,id',
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
