<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('received_by')->nullable()->after('notes');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_receipts', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn('received_by');
        });
    }
};
