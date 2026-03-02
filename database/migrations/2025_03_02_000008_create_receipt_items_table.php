<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('container_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty_received', 12, 2)->default(0);
            $table->decimal('buy_price', 14, 2)->default(0);
            $table->decimal('sale_price', 14, 2)->default(0);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('warehouse_receipt_id');
            $table->index('product_id');
            $table->index('container_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_items');
    }
};
