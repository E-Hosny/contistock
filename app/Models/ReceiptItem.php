<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptItem extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'warehouse_receipt_id',
        'container_id',
        'product_id',
        'qty_received',
        'buy_price',
        'sale_price',
    ];

    protected function casts(): array
    {
        return [
            'qty_received' => 'decimal:2',
            'buy_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
        ];
    }

    public function warehouseReceipt(): BelongsTo
    {
        return $this->belongsTo(WarehouseReceipt::class);
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getProfitPerPieceAttribute(): float
    {
        return max(0, (float) $this->sale_price - (float) $this->buy_price);
    }

    public function getProfitMarginPercentAttribute(): ?float
    {
        $buy = (float) $this->buy_price;
        if ($buy <= 0) {
            return null;
        }

        return round((($this->sale_price - $this->buy_price) / $buy) * 100, 2);
    }

    public function getTotalProfitExpectedAttribute(): float
    {
        return $this->profit_per_piece * (float) $this->qty_received;
    }
}
