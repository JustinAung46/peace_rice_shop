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
        // 1. Update Products table
        DB::statement('ALTER TABLE products MODIFY current_selling_price BIGINT');
        if (Schema::hasColumn('products', 'price_per_pyi')) {
            DB::statement('ALTER TABLE products MODIFY price_per_pyi BIGINT');
        }

        // 2. Update Stock Batches table
        DB::statement('ALTER TABLE stock_batches MODIFY cost_price BIGINT');

        // 3. Update Sales table
        DB::statement('ALTER TABLE sales MODIFY total_amount BIGINT');

        // 4. Update Customers table
        DB::statement('ALTER TABLE customers MODIFY credit_balance BIGINT');

        // 5. Update Sale Items table
        Schema::table('sale_items', function (Blueprint $table) {
            // Drop profit if it exists
            if (Schema::hasColumn('sale_items', 'profit')) {
                $table->dropColumn('profit');
            }
        });

        DB::statement('ALTER TABLE sale_items MODIFY unit_price BIGINT');
        DB::statement('ALTER TABLE sale_items MODIFY total_price BIGINT');
        
        if (Schema::hasColumn('sale_items', 'discount')) {
            DB::statement('ALTER TABLE sale_items MODIFY discount BIGINT');
        }
        if (Schema::hasColumn('sale_items', 'subtotal')) {
            DB::statement('ALTER TABLE sale_items MODIFY subtotal BIGINT');
        }

        Schema::table('sale_items', function (Blueprint $table) {
            // Add batch tracking fields
            $table->foreignId('stock_batch_id')->nullable()->after('product_id')->constrained('stock_batches')->nullOnDelete();
            $table->bigInteger('cost_price')->after('stock_batch_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['stock_batch_id']);
            $table->dropColumn(['stock_batch_id', 'cost_price']);
        });

        DB::statement('ALTER TABLE products MODIFY current_selling_price DECIMAL(12, 2)');
        if (Schema::hasColumn('products', 'price_per_pyi')) {
            DB::statement('ALTER TABLE products MODIFY price_per_pyi DECIMAL(12, 2)');
        }

        DB::statement('ALTER TABLE stock_batches MODIFY cost_price DECIMAL(12, 2)');
        DB::statement('ALTER TABLE sales MODIFY total_amount DECIMAL(12, 2)');
        DB::statement('ALTER TABLE customers MODIFY credit_balance DECIMAL(12, 2)');

        DB::statement('ALTER TABLE sale_items MODIFY unit_price DECIMAL(12, 2)');
        DB::statement('ALTER TABLE sale_items MODIFY total_price DECIMAL(12, 2)');
        
        if (Schema::hasColumn('sale_items', 'discount')) {
            DB::statement('ALTER TABLE sale_items MODIFY discount DECIMAL(12, 2)');
        }
        if (Schema::hasColumn('sale_items', 'subtotal')) {
            DB::statement('ALTER TABLE sale_items MODIFY subtotal DECIMAL(12, 2)');
        }

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('profit', 12, 2)->default(0);
        });
    }
};
