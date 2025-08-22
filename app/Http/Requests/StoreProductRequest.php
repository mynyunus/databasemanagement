<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_group_id' => 'required|uuid|exists:product_groups,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku',
            'is_active' => 'boolean',
            'price_default' => 'required|numeric|min:0|max:999999.99',
            'price_override' => 'nullable|numeric|min:0|max:999999.99',
            'cogs' => 'required|numeric|min:0|max:999999.99',
            'postage_cost' => 'required|numeric|min:0|max:999999.99',
            'bottle_qty' => 'required|integer|min:1|max:999',
        ];
    }

    public function messages(): array
    {
        return [
            'product_group_id.required' => 'Product group is required',
            'product_group_id.exists' => 'Selected product group does not exist',
            'name.required' => 'Product name is required',
            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU already exists',
            'price_default.required' => 'Default price is required',
            'price_default.min' => 'Price must be at least RM 0.00',
            'cogs.required' => 'Cost of goods sold (COGS) is required',
            'postage_cost.required' => 'Postage cost is required',
            'bottle_qty.required' => 'Bottle quantity is required',
            'bottle_qty.min' => 'Bottle quantity must be at least 1',
        ];
    }
}