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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id')->index('sale_items_sale_id_foreign');
            $table->unsignedBigInteger('product_id')->index('sale_items_product_id_foreign');
            $table->unsignedBigInteger('product_variant_id')->nullable()->index('sale_items_product_variant_id_foreign');
            $table->integer('quantity');
            $table->bigInteger('unit_price');
            $table->bigInteger('cost_price')->default(0);
            $table->bigInteger('discount')->default(0);
            $table->bigInteger('subtotal');
            $table->bigInteger('total_price')->default(0);
            $table->bigInteger('total_cost')->default(0);
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
