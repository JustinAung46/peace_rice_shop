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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('customer_id')->nullable()->index('sales_customer_id_foreign');
            $table->bigInteger('total_amount')->nullable();
            $table->string('payment_method')->default('Cash');
            $table->bigInteger('credit_remaining')->default(0);
            $table->enum('status', ['completed', 'cancelled'])->default('completed');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'outstanding', 'completed'])->nullable()->default('paid');
            $table->enum('sale_type', ['retail', 'wholesale'])->default('retail');
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
