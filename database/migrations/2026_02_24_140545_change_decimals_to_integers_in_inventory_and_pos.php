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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_count')->default(0)->change();
            $table->bigInteger('total_cost_value')->default(0)->change();
            $table->integer('pyi_per_bag')->nullable()->change();
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            $table->integer('original_quantity')->change();
            $table->integer('remaining_quantity')->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->bigInteger('total_cost')->default(0)->change();
        });

        Schema::table('sale_item_batches', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock_count', 10, 2)->default(0)->change();
            $table->decimal('total_cost_value', 15, 2)->default(0)->change();
            $table->decimal('pyi_per_bag', 10, 2)->nullable()->change();
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            $table->decimal('original_quantity', 10, 2)->change();
            $table->decimal('remaining_quantity', 10, 2)->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
            $table->decimal('total_cost', 15, 2)->default(0)->change();
        });

        Schema::table('sale_item_batches', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });
    }
};
