<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_item_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_item_id')->index('sale_item_batches_sale_item_id_foreign');
            $table->unsignedBigInteger('stock_batch_id')->index('sale_item_batches_stock_batch_id_foreign');
            $table->integer('quantity');
            $table->bigInteger('cost_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_item_batches');
    }
};
