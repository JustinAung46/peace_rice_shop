<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class POSController extends Controller
{
    public function index()
    {
        // Get products with their total available stock in Shop 1
        $products = Product::with(['stockBatches' => function($query) {
            $query->where('warehouse_id', 1)->where('remaining_quantity', '>', 0);
        }])->get()->map(function($product) {
            $product->stock_count = $product->stockBatches->sum('remaining_quantity');
            return $product;
        });

        return view('pos.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // Create Sale Record
            $sale = Sale::create([
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'total_amount' => 0, // Will update after calculating items
                'payment_method' => $request->payment_method,
            ]);

            $totalSaleAmount = 0;

            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['id']);
                $quantityRequested = $item['quantity'];
                
                // 1. Calculate Revenue for this line item
                $lineTotal = $product->current_selling_price * $quantityRequested;
                $totalSaleAmount += $lineTotal;

                // 2. FIFO Stock Deduction & Profit Calculation
                $remainingToDeduct = $quantityRequested;
                $totalCostForitem = 0;

                // Get batches for this product in Shop 1 (ID: 1), ordered by date
                $batches = StockBatch::where('product_id', $product->id)
                    ->where('warehouse_id', 1) // Shop 1
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('purchase_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingToDeduct <= 0) break;

                    $take = min($batch->remaining_quantity, $remainingToDeduct);
                    
                    // Cost for this portion
                    $totalCostForitem += ($take * $batch->cost_price);

                    $batch->decrement('remaining_quantity', $take);
                    $remainingToDeduct -= $take;
                }

                if ($remainingToDeduct > 0) {
                    throw new \Exception("Not enough stock for {$product->name}. Missing: {$remainingToDeduct}");
                }

                // 3. Create SaleItem
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantityRequested,
                    'unit_price' => $product->current_selling_price,
                    'total_price' => $lineTotal,
                    'profit' => $lineTotal - $totalCostForitem, // Exact Profit
                ]);
            }

            $sale->update(['total_amount' => $totalSaleAmount]);

            DB::commit();

            return response()->json(['success' => true, 'invoice' => $sale->invoice_number]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
