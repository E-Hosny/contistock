<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tenant_id !== null;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'total_weight_kg' => ['nullable', 'numeric', 'min:0'],
            'total_cost' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'invoice_ref' => ['nullable', 'string', 'max:255'],
            'status' => ['string', 'in:draft,purchased,received_to_warehouse,closed'],
        ];
    }
}
