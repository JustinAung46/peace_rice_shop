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
        Schema::create('credit_payment_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Nullable because the payment row is deleted on 'deleted' action
            $table->unsignedBigInteger('credit_payment_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->index();

            // 'created' | 'edited' | 'deleted'
            $table->string('action', 20);

            // Before/after snapshot
            $table->bigInteger('old_amount')->nullable();
            $table->bigInteger('new_amount')->nullable();
            $table->text('old_note')->nullable();
            $table->text('new_note')->nullable();

            // Who did it and from where
            $table->unsignedBigInteger('performed_by')->index();
            $table->string('ip_address', 45)->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_payment_logs');
    }
};
