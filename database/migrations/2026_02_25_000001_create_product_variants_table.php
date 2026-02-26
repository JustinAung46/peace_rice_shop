<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create product_variants table
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');                          // e.g. "Old 6 Pyi", "1 Litre"
            $table->string('sku')->nullable()->unique();
            $table->string('unit_label')->default('Unit'); // e.g. "Bag", "L", "kg"
            $table->bigInteger('selling_price')->default(0);
            $table->integer('pyi_per_bag')->nullable();      // rice-specific for transform
            $table->bigInteger('price_per_pyi')->nullable(); // rice-specific
            $table->timestamps();
        });

        // 2. Strip variant-specific columns from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['current_selling_price', 'price_per_pyi', 'pyi_per_bag']);
            // SKU is nullable+unique; we drop it too since it moves to variants
            $table->dropUnique(['sku']);
            $table->dropColumn('sku');
        });

        // 3. Add product_variant_id to stock_batches
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->foreignId('product_variant_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('product_variants')
                  ->onDelete('cascade');
        });

        // 4. Add product_variant_id to sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('product_variants')
                  ->onDelete('cascade');
        });

        // 5. Add product_variant_id + target_variant_id to stock_movements
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('product_variant_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('product_variants')
                  ->onDelete('set null');
            $table->foreignId('target_variant_id')
                  ->nullable()
                  ->after('target_product_id')
                  ->constrained('product_variants')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Remove from stock_movements
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['target_variant_id']);
            $table->dropColumn('target_variant_id');
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });

        // Remove from sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });

        // Remove from stock_batches
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });

        // Restore products columns
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique();
            $table->bigInteger('current_selling_price')->default(0);
            $table->bigInteger('price_per_pyi')->nullable();
            $table->integer('pyi_per_bag')->nullable();
        });

        Schema::dropIfExists('product_variants');
    }
};
