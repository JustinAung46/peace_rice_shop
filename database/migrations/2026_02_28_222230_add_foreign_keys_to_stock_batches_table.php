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
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['product_variant_id'])->references(['id'])->on('product_variants')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['warehouse_id'])->references(['id'])->on('warehouses')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropForeign('stock_batches_product_id_foreign');
            $table->dropForeign('stock_batches_product_variant_id_foreign');
            $table->dropForeign('stock_batches_warehouse_id_foreign');
        });
    }
};
