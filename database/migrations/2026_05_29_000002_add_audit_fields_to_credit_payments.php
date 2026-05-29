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
        Schema::table('credit_payments', function (Blueprint $table) {
            // Stores the amount as first recorded — never changed after creation
            $table->bigInteger('original_amount')->nullable()->after('amount');
            // User who last edited this record
            $table->unsignedBigInteger('updated_by')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_payments', function (Blueprint $table) {
            $table->dropColumn(['original_amount', 'updated_by']);
        });
    }
};
