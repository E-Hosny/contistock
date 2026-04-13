<?php

namespace App\Http\Requests;

use App\Models\Container;
use Illuminate\Foundation\Http\FormRequest;

class StoreContainerExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $container = $this->route('container');

        return $container instanceof Container
            && $this->user()->can('update', $container)
            && $container->canEditPurchasePlan();
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
