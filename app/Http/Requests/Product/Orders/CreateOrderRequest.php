<?php

namespace App\Http\Requests\Product\Orders;

use App\Http\Requests\CompanyScopedRequest;

class CreateOrderRequest extends CompanyScopedRequest
{
    public function rules(): array
    {
        return array_merge([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'labels' => 'nullable|array',
            'labels.*' => ['required', function ($attribute, $value, $fail) {
                if (!is_int($value) && !is_string($value)) {
                    $fail("Each label must be an integer ID or a string name.");
                }
                if (is_int($value) && !\App\Models\Label::withoutGlobalScopes()->where('id', $value)->exists()) {
                    $fail("Label with ID {$value} does not exist.");
                }
            }],
        ], ['company_id' => 'prohibited']);
    }

    public function messages(): array
    {
        return array_merge([
            'items.required' => 'The items are required.',
            'items.array' => 'The items must be an array.',
            'items.*.id.required' => 'Each item must have an item_id.',
            'items.*.id.exists' => 'The selected item does not exist.',
            'items.*.quantity.required' => 'Each item must have a quantity.',
            'items.*.quantity.integer' => 'The quantity must be an integer.',
            'items.*.quantity.min' => 'The quantity must be at least 1.',
        ], $this->baseMessages());
    }
}