<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipt_items', function (Blueprint $table) {
            $table->dropForeign(['warehouse_receipt_id']);
        });

        Schema::table('receipt_items', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_receipt_id')->nullable()->change();
        });

        Schema::table('receipt_items', function (Blueprint $table) {
            $table->foreign('warehouse_receipt_id')
                ->references('id')
                ->on('warehouse_receipts')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('receipt_items', function (Blueprint $table) {
            $table->dropForeign(['warehouse_receipt_id']);
        });

        Schema::table('receipt_items', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_receipt_id')->nullable(false)->change();
        });

        Schema::table('receipt_items', function (Blueprint $table) {
            $table->foreign('warehouse_receipt_id')
                ->references('id')
                ->on('warehouse_receipts')
                ->cascadeOnDelete();
        });
    }
};
