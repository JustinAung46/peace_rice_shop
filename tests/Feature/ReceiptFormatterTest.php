<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Services\ReceiptFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * ReceiptFormatter Feature Tests
 *
 * Uses RefreshDatabase so Setting::get() can hit the DB. The Sale graph
 * is built with Mockery so we don't need full factory migrations.
 *
 * Run: php artisan test --filter=ReceiptFormatterTest
 */

uses(RefreshDatabase::class);

// ── Helper ────────────────────────────────────────────────────────────────────

function makeSale(
    array $itemDefs    = [['name' => 'Fragrant Rice', 'variant' => '50kg', 'qty' => 2, 'price' => 50000, 'discount' => 0]],
    array $paymentDefs = [['method' => 'Cash', 'amount' => 100000]],
    ?string $customerName = null
): Sale {
    $sale = Mockery::mock(Sale::class)->makePartial();

    $sale->invoice_number = 'INV-TEST-001';
    $sale->total_amount   = collect($itemDefs)->sum(fn ($i) => $i['qty'] * $i['price'] - $i['discount']);
    $sale->created_at     = now();

    if ($customerName !== null) {
        $customer       = Mockery::mock(Customer::class)->makePartial();
        $customer->name = $customerName;
        $sale->customer = $customer;
    } else {
        $sale->customer = null;
    }

    $items = collect($itemDefs)->map(function ($def) {
        $product       = Mockery::mock(Product::class)->makePartial();
        $product->name = $def['name'];

        $variant       = Mockery::mock(ProductVariant::class)->makePartial();
        $variant->name = $def['variant'];

        $item               = Mockery::mock(SaleItem::class)->makePartial();
        $item->product      = $product;
        $item->variant      = $variant;
        $item->quantity     = $def['qty'];
        $item->unit_price   = $def['price'];
        $item->discount     = $def['discount'];
        $item->subtotal     = $def['qty'] * $def['price'];

        return $item;
    });

    $payments = collect($paymentDefs)->map(function ($def) {
        $p                 = Mockery::mock(SalePayment::class)->makePartial();
        $p->payment_method = $def['method'];
        $p->amount         = $def['amount'];
        return $p;
    });

    $sale->shouldReceive('load')->andReturnSelf();
    $sale->items    = $items;
    $sale->payments = $payments;

    return $sale;
}

// ─── Tests ────────────────────────────────────────────────────────────────────

test('format() returns a non-empty array', function () {
    $segments = (new ReceiptFormatter)->format(makeSale());
    expect($segments)->toBeArray()->not->toBeEmpty();
});

test('GRAND TOTAL segment is always present', function () {
    $segments = (new ReceiptFormatter)->format(makeSale());
    $lefts    = array_column($segments, 'left');
    expect($lefts)->toContain('GRAND TOTAL');
});

test('grand total value matches sale total_amount', function () {
    $sale     = makeSale([['name' => 'Rice', 'variant' => '25kg', 'qty' => 1, 'price' => 30000, 'discount' => 0]]);
    $segments = (new ReceiptFormatter)->format($sale);

    $totalSeg = collect($segments)->first(fn ($s) => ($s['left'] ?? '') === 'GRAND TOTAL');
    expect($totalSeg)->not->toBeNull();
    expect($totalSeg['right'])->toContain('30,000');
});

test('customer name appears in segments when set', function () {
    $segments = (new ReceiptFormatter)->format(makeSale(customerName: 'Daw Aye'));
    $lefts    = array_column($segments, 'left');
    expect($lefts)->toContain('Daw Aye');
});

test('Walk-in is used when customer is null', function () {
    $segments = (new ReceiptFormatter)->format(makeSale(customerName: null));
    $lefts    = array_column($segments, 'left');
    expect($lefts)->toContain('Walk-in');
});

test('item discount segment is present when discount > 0', function () {
    $sale     = makeSale([['name' => 'Broken Rice', 'variant' => '10kg', 'qty' => 1, 'price' => 10000, 'discount' => 500]]);
    $segments = (new ReceiptFormatter)->format($sale);
    $lefts    = array_column($segments, 'left');
    expect($lefts)->toContain('  Discount');
});

test('item discount segment is absent when discount is 0', function () {
    $sale     = makeSale([['name' => 'Rice', 'variant' => '5kg', 'qty' => 1, 'price' => 5000, 'discount' => 0]]);
    $segments = (new ReceiptFormatter)->format($sale);
    $lefts    = array_column($segments, 'left');
    expect($lefts)->not->toContain('  Discount');
});

test('payment method appears in segments', function () {
    $sale     = makeSale(paymentDefs: [['method' => 'KBZ Pay', 'amount' => 50000]]);
    $segments = (new ReceiptFormatter)->format($sale);
    $lefts    = array_column($segments, 'left');
    // ucfirst() leaves an already-uppercase string unchanged
    expect($lefts)->toContain('KBZ Pay');
});

test('product variant name is included in item segment', function () {
    $sale     = makeSale([['name' => 'Rice', 'variant' => '50kg Bag', 'qty' => 1, 'price' => 20000, 'discount' => 0]]);
    $segments = (new ReceiptFormatter)->format($sale);
    $lefts    = array_column($segments, 'left');
    $found    = collect($lefts)->first(fn ($l) => str_contains((string) $l, '50kg Bag'));
    expect($found)->not->toBeNull();
});

test('all segments have a left key', function () {
    $segments = (new ReceiptFormatter)->format(makeSale());
    foreach ($segments as $seg) {
        expect($seg)->toHaveKey('left');
    }
});

test('shop name segment falls back to config default when no DB setting', function () {
    $segments  = (new ReceiptFormatter)->format(makeSale());
    $firstLeft = $segments[0]['left'];
    // config() default is used when settings table is empty (RefreshDatabase wipes it)
    expect($firstLeft)->toBe(config('shop.name'));
});
