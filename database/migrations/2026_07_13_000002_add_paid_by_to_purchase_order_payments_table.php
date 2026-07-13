<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('paid_by')->nullable()->after('note');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_payments', function (Blueprint $table) {
            $table->dropForeign(['paid_by']);
            $table->dropColumn('paid_by');
        });
    }
};
