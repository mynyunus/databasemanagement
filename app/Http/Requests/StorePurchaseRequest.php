<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|uuid|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001|max:999.999',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.price_each' => 'required|numeric|min:0|max:999999.99',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'At least one purchase item is required',
            'items.min' => 'At least one purchase item is required',
            'items.*.quantity.required' => 'Item quantity is required',
            'items.*.quantity.min' => 'Quantity must be greater than 0',
            'items.*.price_each.required' => 'Item price is required',
            'items.*.price_each.min' => 'Price must be at least RM 0.00',
            'items.*.product_id.exists' => 'Selected product does not exist',
        ];
    }
}