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
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreign(['from_warehouse_id'])->references(['id'])->on('warehouses')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['product_variant_id'])->references(['id'])->on('product_variants')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['target_product_id'])->references(['id'])->on('products')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['target_variant_id'])->references(['id'])->on('product_variants')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['to_warehouse_id'])->references(['id'])->on('warehouses')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign('stock_movements_from_warehouse_id_foreign');
            $table->dropForeign('stock_movements_product_id_foreign');
            $table->dropForeign('stock_movements_product_variant_id_foreign');
            $table->dropForeign('stock_movements_target_product_id_foreign');
            $table->dropForeign('stock_movements_target_variant_id_foreign');
            $table->dropForeign('stock_movements_to_warehouse_id_foreign');
            $table->dropForeign('stock_movements_user_id_foreign');
        });
    }
};
