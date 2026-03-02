<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('product_name'); // product type / container name
            $table->text('description')->nullable();
            $table->decimal('total_weight_kg', 12, 2)->nullable();
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->date('purchase_date')->nullable();
            $table->string('invoice_ref')->nullable();
            $table->string('status')->default('draft'); // draft, purchased, received_to_warehouse, closed
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
