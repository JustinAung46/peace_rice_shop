<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_receipt_item_id')
                  ->nullable()
                  ->after('batch_code')
                  ->index();

            $table->foreign('purchase_order_receipt_item_id')
                  ->references('id')
                  ->on('purchase_order_receipt_items')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_receipt_item_id']);
            $table->dropColumn('purchase_order_receipt_item_id');
        });
    }
};
