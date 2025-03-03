<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->vendors()->exists($this->vendor->id);
    }

    public function rules()
    {
        return [
            'brand_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'sometimes|string|max:15',
            'vendor_images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'regions' => 'sometimes|array',
            'regions.*.region_id' => 'required|exists:regions,id',
            'regions.*.delivery_cost' => 'nullable|numeric',
            'regions.*.discount' => 'nullable|integer',
            'regions.*.description' => 'nullable|string',
        ];
    }
}

