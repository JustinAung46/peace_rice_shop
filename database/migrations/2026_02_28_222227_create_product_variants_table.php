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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index('product_variants_product_id_foreign');
            $table->string('name');
            $table->string('sku')->nullable()->unique();
            $table->string('unit_label')->default('Unit');
            $table->bigInteger('selling_price')->default(0);
            $table->integer('pyi_per_bag')->nullable();
            $table->bigInteger('price_per_pyi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
