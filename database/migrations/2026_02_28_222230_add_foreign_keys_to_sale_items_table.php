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
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['product_variant_id'])->references(['id'])->on('product_variants')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['sale_id'])->references(['id'])->on('sales')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign('sale_items_product_id_foreign');
            $table->dropForeign('sale_items_product_variant_id_foreign');
            $table->dropForeign('sale_items_sale_id_foreign');
        });
    }
};
