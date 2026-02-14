<?php

namespace App\Services;

use App\Models\StockBatch;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    public function transfer($productId, $fromId, $toId, $quantityToTransfer)
    {
        return DB::transaction(function () use ($productId, $fromId, $toId, $quantityToTransfer) {

            $batches = StockBatch::where('product_id', $productId)
                ->where('warehouse_id', $fromId)
                ->where('remaining_quantity', '>', 0)
                ->orderBy('purchase_date', 'asc')
                ->lockForUpdate()
                ->get();

            $totalAvailable = $batches->sum('remaining_quantity');

            if ($totalAvailable < $quantityToTransfer) {
                throw new \Exception("Not enough stock in source warehouse.");
            }

            $remainingToTransfer = $quantityToTransfer;

            foreach ($batches as $batch) {

                if ($remainingToTransfer <= 0) break;

                $take = min($batch->remaining_quantity, $remainingToTransfer);

                // Deduct from source
                $batch->decrement('remaining_quantity', $take);

                // Create batch in destination
                StockBatch::create([
                    'product_id' => $productId,
                    'warehouse_id' => $toId,
                    'original_quantity' => $take,
                    'remaining_quantity' => $take,
                    'cost_price' => $batch->cost_price,
                    'purchase_date' => $batch->purchase_date,
                    'batch_code' => $batch->batch_code ? $batch->batch_code . '-TR' : null,
                ]);

                $remainingToTransfer -= $take;
            }

            // 🔥 Optional: record transfer log table here

            return true;
        });
    }
}
