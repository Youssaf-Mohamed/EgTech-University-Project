<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorStoreRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->is_active;
    }

    public function rules()
    {
        return [
            'brand_name' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'required|string|max:15',
            'vendor_images' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'regions' => 'required|array',
            'regions.*.region_id' => 'required|exists:regions,id',
            'regions.*.delivery_cost' => 'nullable|numeric',
            'regions.*.discount' => 'nullable|integer',
            'regions.*.description' => 'nullable|string',
        ];
    }
}
