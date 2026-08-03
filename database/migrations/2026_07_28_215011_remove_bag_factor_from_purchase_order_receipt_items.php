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
        Schema::table('purchase_order_receipt_items', function (Blueprint $table) {
            $table->dropColumn('bag_factor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_receipt_items', function (Blueprint $table) {
            $table->decimal('bag_factor', 8, 4)->nullable()->after('landed_cost');
        });
    }
};
