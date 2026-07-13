<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add composite indexes to fix query timeout issues.
     *
     * Key patterns being indexed:
     *  - stock_batches: FIFO queries filter by (variant_id, warehouse_id, remaining_qty > 0)
     *  - sale_items: reports join/filter by (sale_id, created_at) and aggregate by product_id
     *  - sales: dashboard/reports filter by (status, created_at)
     */
    public function up(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            // Used by FIFO deduction and stock sum queries
            $table->index(
                ['product_variant_id', 'warehouse_id', 'remaining_quantity'],
                'idx_stock_batches_variant_wh_qty'
            );
            // Used by stock sum queries on dashboard (withSum across all warehouses)
            $table->index(
                ['product_id', 'remaining_quantity'],
                'idx_stock_batches_product_qty'
            );
        });

        Schema::table('sale_items', function (Blueprint $table) {
            // Report queries aggregate by product_id filtered by created_at
            $table->index(
                ['created_at', 'product_id'],
                'idx_sale_items_created_product'
            );
            // Dashboard top-selling: group by product_id filtered via sale join
            $table->index(
                ['sale_id', 'product_id'],
                'idx_sale_items_sale_product'
            );
        });

        Schema::table('sales', function (Blueprint $table) {
            // Dashboard and reports filter heavily on status + created_at
            $table->index(
                ['status', 'created_at'],
                'idx_sales_status_created'
            );
        });
    }

    public function down(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropIndex('idx_stock_batches_variant_wh_qty');
            $table->dropIndex('idx_stock_batches_product_qty');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex('idx_sale_items_created_product');
            $table->dropIndex('idx_sale_items_sale_product');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('idx_sales_status_created');
        });
    }
};
