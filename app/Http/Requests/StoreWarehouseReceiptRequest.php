<?php

namespace App\Http\Requests;

use App\Models\WarehouseReceipt;
use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', WarehouseReceipt::class);
    }

    public function rules(): array
    {
        return [
            'container_id' => ['required', 'exists:containers,id'],
            'receipt_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty_received' => ['required', 'numeric', 'min:0.01'],
            'items.*.buy_price' => ['required', 'numeric', 'min:0'],
            'items.*.sale_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
