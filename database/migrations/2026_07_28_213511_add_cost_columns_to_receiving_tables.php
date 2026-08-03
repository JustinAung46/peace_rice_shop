<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_order_receipts', function (Blueprint $table) {
            $table->integer('delivery_rate')->nullable()->after('notes');
        });

        Schema::table('purchase_order_receipt_items', function (Blueprint $table) {
            $table->bigInteger('purchase_cost')->nullable()->after('quantity');
            $table->bigInteger('delivery_cost')->nullable()->after('purchase_cost');
            $table->bigInteger('landed_cost')->nullable()->after('delivery_cost');
            $table->decimal('bag_factor', 8, 4)->nullable()->after('landed_cost');
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            $table->bigInteger('purchase_cost')->nullable()->after('cost_price');
            $table->bigInteger('delivery_cost')->nullable()->after('purchase_cost');
            $table->bigInteger('landed_cost')->nullable()->after('delivery_cost');
        });

        // Data migration for existing stock_batches
        DB::table('stock_batches')->orderBy('id')->chunk(100, function ($batches) {
            foreach ($batches as $batch) {
                DB::table('stock_batches')
                    ->where('id', $batch->id)
                    ->update([
                        'purchase_cost' => $batch->cost_price,
                        'delivery_cost' => 0,
                        'landed_cost'   => $batch->cost_price,
                    ]);
            }
        });

        // Data migration for existing purchase_order_receipt_items
        DB::table('purchase_order_receipt_items')->orderBy('id')->chunk(100, function ($items) {
            foreach ($items as $item) {
                $poItem = DB::table('purchase_order_items')->where('id', $item->purchase_order_item_id)->first();
                if ($poItem) {
                    DB::table('purchase_order_receipt_items')
                        ->where('id', $item->id)
                        ->update([
                            'purchase_cost' => $poItem->cost_price,
                            'delivery_cost' => 0,
                            'landed_cost'   => $poItem->cost_price,
                        ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropColumn(['purchase_cost', 'delivery_cost', 'landed_cost']);
        });

        Schema::table('purchase_order_receipt_items', function (Blueprint $table) {
            $table->dropColumn(['purchase_cost', 'delivery_cost', 'landed_cost', 'bag_factor']);
        });

        Schema::table('purchase_order_receipts', function (Blueprint $table) {
            $table->dropColumn('delivery_rate');
        });
    }
};
