<?php

namespace App\Services;

use App\Models\Sale;

class ReceiptFormatter
{
    /**
     * Professional POS-style thermal receipt format
     * Clean layout similar to common retail POS systems
     */
    public function format(Sale $sale): array
    {
        $sale->load([
            'items.product',
            'items.variant',
            'payments',
            'customer'
        ]);

        $segments = [];
        $line = '==============================';
        $subLine = '--------------------------------';

        /*
        |--------------------------------------------------------------------------
        | SHOP HEADER
        |--------------------------------------------------------------------------
        */

        $segments[] = [
            'left' => 'ငြိမ်းချမ်း',
            'size' => 44.0,
            'bold' => true,
            'center' => true,
            'spaceBefore' => 1,
            'spaceAfter' => 5
        ];

        $segments[] = [
            'left' => 'ဆန်ရောင်းဝယ်ရေး',
            'size' => 42.0,
            'bold' => true,
            'center' => true,
            'spaceBefore' => 10,
            'spaceAfter' => 5
        ];

        $segments[] = [
            'left' => 'ရပ်ကွက်(၉) ဘူတာလမ်း လားရှိုးမြို့',
            'size' => 24.0,
            'center' => true
        ];

        $segments[] = [
            'left' => 'Tel: 09-788024237, 09-5370682',
            'size' => 22.0,
            'center' => true,
            'spaceAfter' => 5
        ];

        $segments[] = [
            'left' => $line,
            'center' => true
        ];

        /*
        |--------------------------------------------------------------------------
        | SALE INFORMATION
        |--------------------------------------------------------------------------
        */

        $segments[] = [
            'left' => $sale->invoice_number,
            'right' => $sale->created_at->format('d/m/Y'),
            'size' => 22.0,
            'spaceBefore' => 5
        ];

        $customerName = $sale->customer
            ? $sale->customer->name
            : 'Walk-in';

        $segments[] = [
            'left' => $customerName,
            'right' => $sale->created_at->format('h:i A'),
            'size' => 22.0
        ];

        $segments[] = [
            'left' => $subLine,
            'center' => true
        ];

        /*
        |--------------------------------------------------------------------------
        | ITEMS HEADER
        |--------------------------------------------------------------------------
        */

        $segments[] = [
            'left' => 'ITEMS',
            'right' => 'AMOUNT',
            'bold' => true,
            'size' => 24.0,
            'spaceBefore' => 5,
            'spaceAfter' => 5
        ];

        /*
        |--------------------------------------------------------------------------
        | ITEMS LIST
        |--------------------------------------------------------------------------
        */

        foreach ($sale->items as $item) {
            $productName = $item->product->name;

            if ($item->variant) {
                $productName .= ' (' . $item->variant->name . ')';
            }

            // Product Name
            $segments[] = [
                'left' => $productName,
                'size' => 24.0,
                'bold' => true,
                'spaceBefore' => 5
            ];

            // Qty x Unit Price
            $segments[] = [
                'left' => '  ' . $item->quantity . ' x ' . number_format($item->unit_price),
                'right' => number_format($item->quantity * $item->unit_price) . ' MMK',
                'size' => 23.0,
                'spaceAfter' => 2
            ];

            // Item discount
            if ($item->discount > 0) {
                $segments[] = [
                    'left' => '  Discount',
                    'right' => '-' . number_format($item->discount),
                    'size' => 22.0,
                    'spaceAfter' => 2
                ];
            }
        }

        $segments[] = [
            'left' => $subLine,
            'center' => true,
            'spaceBefore' => 8
        ];

        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        $subtotal = $sale->items->sum('subtotal');
        $totalDiscount = $sale->items->sum('discount');

        $segments[] = [
            'left' => 'Subtotal',
            'right' => number_format($subtotal) . ' MMK',
            'size' => 24.0
        ];

        if ($totalDiscount > 0) {
            $segments[] = [
                'left' => 'Discount',
                'right' => '-' . number_format($totalDiscount) . ' MMK',
                'size' => 24.0
            ];
        }

        $segments[] = [
            'left' => 'GRAND TOTAL',
            'right' => number_format($sale->total_amount) . ' MMK',
            'bold' => true,
            'size' => 28.0,
            'spaceBefore' => 8,
            'spaceAfter' => 8
        ];

        $segments[] = [
            'left' => $subLine,
            'center' => true
        ];

        /*
        |--------------------------------------------------------------------------
        | PAYMENTS
        |--------------------------------------------------------------------------
        */

        foreach ($sale->payments as $payment) {
            $segments[] = [
                'left' => ucfirst($payment->payment_method),
                'right' => number_format($payment->amount) . ' MMK',
                'size' => 24.0,
                'spaceBefore' => 3
            ];
        }

        $segments[] = [
            'left' => $line,
            'center' => true,
            'spaceBefore' => 12
        ];

        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        $segments[] = [
            'left' => 'Thank You, Please Come Again',
            'center' => true,
            'size' => 22.0,
            'spaceAfter' => 5
        ];

        $segments[] = [
            'left' => 'Powered by Peace POS',
            'center' => true,
            'size' => 18.0,
            'spaceAfter' => 30
        ];

        return $segments;
    }
}