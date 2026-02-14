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
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('status', ['completed', 'cancelled'])->default('completed')->after('payment_method');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('paid')->after('status');
            $table->enum('sale_type', ['retail', 'wholesale'])->default('retail')->after('payment_status');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_status', 'sale_type']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('profit', 10, 2)->default(0);
        });
    }
};
