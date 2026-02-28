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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->unsignedBigInteger('product_id')->index('stock_movements_product_id_foreign');
            $table->unsignedBigInteger('product_variant_id')->nullable()->index('stock_movements_product_variant_id_foreign');
            $table->unsignedBigInteger('from_warehouse_id')->nullable()->index('stock_movements_from_warehouse_id_foreign');
            $table->unsignedBigInteger('to_warehouse_id')->nullable()->index('stock_movements_to_warehouse_id_foreign');
            $table->unsignedBigInteger('target_product_id')->nullable()->index('stock_movements_target_product_id_foreign');
            $table->unsignedBigInteger('target_variant_id')->nullable()->index('stock_movements_target_variant_id_foreign');
            $table->integer('quantity');
            $table->string('reference_code')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('stock_movements_user_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
