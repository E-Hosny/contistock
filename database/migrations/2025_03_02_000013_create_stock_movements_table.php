<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('container_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('qty', 12, 2); // positive for in, negative for out; or use type + abs qty
            $table->string('type'); // in, out, adjust
            $table->string('ref_type')->nullable(); // warehouse_receipt, sale, etc.
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->date('movement_date');
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('product_id');
            $table->index('container_id');
            $table->index(['tenant_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
