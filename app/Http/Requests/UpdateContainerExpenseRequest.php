<?php

namespace App\Http\Requests;

use App\Models\Container;
use App\Models\ContainerExpense;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContainerExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $container = $this->route('container');
        $expense = $this->route('expense');

        return $container instanceof Container
            && $expense instanceof ContainerExpense
            && (int) $expense->container_id === (int) $container->id
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
