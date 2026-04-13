<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Container extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'product_name',
        'description',
        'total_weight_kg',
        'total_cost',
        'purchase_date',
        'invoice_ref',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_weight_kg' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'purchase_date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function warehouseReceipts(): HasMany
    {
        return $this->hasMany(WarehouseReceipt::class);
    }

    public function receiptItems(): HasMany
    {
        return $this->hasMany(ReceiptItem::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ContainerExpense::class);
    }

    /**
     * Purchase lines not yet linked to a warehouse receipt.
     */
    public function pendingReceiptItems(): HasMany
    {
        return $this->receiptItems()->whereNull('warehouse_receipt_id');
    }

    public function canEditPurchasePlan(): bool
    {
        return in_array($this->status, ['draft', 'purchased'], true);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->supplierPayments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total_cost - $this->paid_amount);
    }
}
