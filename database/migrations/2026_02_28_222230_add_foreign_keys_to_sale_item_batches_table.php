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
        Schema::table('sale_item_batches', function (Blueprint $table) {
            $table->foreign(['sale_item_id'])->references(['id'])->on('sale_items')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['stock_batch_id'])->references(['id'])->on('stock_batches')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_item_batches', function (Blueprint $table) {
            $table->dropForeign('sale_item_batches_sale_item_id_foreign');
            $table->dropForeign('sale_item_batches_stock_batch_id_foreign');
        });
    }
};
