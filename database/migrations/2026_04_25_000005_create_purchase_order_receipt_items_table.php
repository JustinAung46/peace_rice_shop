<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_receipt_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_order_receipt_id')->index();
            $table->unsignedBigInteger('purchase_order_item_id')->index();
            $table->unsignedBigInteger('warehouse_id')->index();
            $table->integer('quantity');   // qty stored in this specific warehouse
            $table->timestamps();

            $table->foreign('purchase_order_receipt_id')->references('id')->on('purchase_order_receipts')->onDelete('cascade');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_receipt_items');
    }
};
