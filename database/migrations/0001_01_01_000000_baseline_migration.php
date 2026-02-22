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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('account_id')->unique();
            $table->string('role')->default('employee');
            $table->json('permissions')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('stock_count', 10, 2)->default(0);
            $table->decimal('total_cost_value', 15, 2)->default(0);
            $table->bigInteger('current_selling_price')->default(0);
            $table->bigInteger('price_per_pyi')->nullable();
            $table->decimal('pyi_per_bag', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->bigInteger('credit_balance')->default(0);
            $table->timestamps();
        });

        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->decimal('original_quantity', 10, 2);
            $table->decimal('remaining_quantity', 10, 2);
            $table->bigInteger('cost_price')->nullable();
            $table->date('purchase_date');
            $table->string('batch_code')->nullable();
            $table->timestamps();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->bigInteger('total_amount')->nullable();
            $table->string('payment_method')->default('Cash');
            $table->enum('status', ['completed', 'cancelled'])->default('completed');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('paid');
            $table->enum('sale_type', ['retail', 'wholesale'])->default('retail');
            $table->timestamps();

            // Indexes for performance
            $table->index('created_at');
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->bigInteger('unit_price');
            $table->bigInteger('cost_price')->default(0);
            $table->bigInteger('discount')->default(0);
            $table->bigInteger('subtotal');
            $table->bigInteger('total_price')->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index('created_at');
        });

        Schema::create('sale_item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('stock_batch_id')->constrained('stock_batches')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->bigInteger('cost_price');
            $table->timestamps();
        });

        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->string('payment_method');
            $table->bigInteger('amount');
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // in, out, transfer, return, convert
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->foreignId('target_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->decimal('quantity', 10, 2);
            $table->string('reference_code')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_item_batches');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('stock_batches');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
