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
        // First add the new columns
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('bag_factor', 8, 4)->nullable()->after('selling_price');
            $table->bigInteger('retail_price')->nullable()->after('bag_factor');
        });

        // Migrate existing data if any
        DB::table('product_variants')->orderBy('id')->chunk(100, function ($variants) {
            foreach ($variants as $variant) {
                if ($variant->pyi_per_bag) {
                    $bagFactor = $variant->pyi_per_bag / 24; // Assuming 24 pyi = 1 full bag
                    $retailPrice = ($variant->price_per_pyi ?? 0) * $variant->pyi_per_bag;

                    DB::table('product_variants')->where('id', $variant->id)->update([
                        'bag_factor' => $bagFactor,
                        'retail_price' => $retailPrice > 0 ? $retailPrice : null,
                    ]);
                }
            }
        });

        // Drop the old columns
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['pyi_per_bag', 'price_per_pyi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('pyi_per_bag')->nullable();
            $table->bigInteger('price_per_pyi')->nullable();
        });

        DB::table('product_variants')->orderBy('id')->chunk(100, function ($variants) {
            foreach ($variants as $variant) {
                if ($variant->bag_factor) {
                    $pyiPerBag = round($variant->bag_factor * 24);
                    $pricePerPyi = $variant->retail_price && $pyiPerBag > 0 
                        ? round($variant->retail_price / $pyiPerBag) 
                        : null;

                    DB::table('product_variants')->where('id', $variant->id)->update([
                        'pyi_per_bag' => $pyiPerBag,
                        'price_per_pyi' => $pricePerPyi,
                    ]);
                }
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['bag_factor', 'retail_price']);
        });
    }
};
