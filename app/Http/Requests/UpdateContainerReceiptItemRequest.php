<?php

namespace App\Http\Requests;

use App\Models\Container;
use App\Models\ReceiptItem;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContainerReceiptItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $container = $this->route('container');
        $receiptItem = $this->route('receiptItem');

        return $container instanceof Container
            && $receiptItem instanceof ReceiptItem
            && (int) $receiptItem->container_id === (int) $container->id
            && $receiptItem->warehouse_receipt_id === null
            && $this->user()->can('update', $container)
            && $container->canEditPurchasePlan();
    }

    public function rules(): array
    {
        return [
            'qty_received' => ['required', 'numeric', 'min:0.01'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
