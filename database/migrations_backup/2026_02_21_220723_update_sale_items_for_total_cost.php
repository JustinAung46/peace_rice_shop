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
            $table->bigInteger('total_cost')->after('cost_price')->default(0);
        });

        // Seed existing total_cost
        \Illuminate\Support\Facades\DB::statement('UPDATE sale_items SET total_cost = cost_price * quantity');

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['stock_batch_id']);
            $table->dropColumn('stock_batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('stock_batch_id')->nullable()->after('product_id')->constrained('stock_batches')->nullOnDelete();
            $table->dropColumn('total_cost');
        });
    }
};
