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
        Schema::table('credit_allocations', function (Blueprint $table) {
            $table->foreign(['credit_payment_id'])->references(['id'])->on('credit_payments')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['sale_id'])->references(['id'])->on('sales')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_allocations', function (Blueprint $table) {
            $table->dropForeign('credit_allocations_credit_payment_id_foreign');
            $table->dropForeign('credit_allocations_sale_id_foreign');
        });
    }
};
