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
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index('stock_batches_product_id_foreign');
            $table->unsignedBigInteger('product_variant_id')->nullable()->index('stock_batches_product_variant_id_foreign');
            $table->unsignedBigInteger('warehouse_id')->index('stock_batches_warehouse_id_foreign');
            $table->integer('original_quantity');
            $table->integer('remaining_quantity');
            $table->bigInteger('cost_price')->nullable();
            $table->date('purchase_date');
            $table->string('batch_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
