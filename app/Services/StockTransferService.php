<?php

namespace App\Services;

use App\Models\StockBatch;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    /**
     * Transfer stock for a specific product variant between warehouses (FIFO).
     */
    public function transferVariant($variantId, $fromId, $toId, $quantityToTransfer)
    {
        return DB::transaction(function () use ($variantId, $fromId, $toId, $quantityToTransfer) {

            $batches = StockBatch::where('product_variant_id', $variantId)
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
            $firstBatch = $batches->first();

            foreach ($batches as $batch) {
                if ($remainingToTransfer <= 0) break;

                $take = min($batch->remaining_quantity, $remainingToTransfer);

                $batch->decrement('remaining_quantity', $take);

                StockBatch::create([
                    'product_id'         => $batch->product_id,
                    'product_variant_id' => $variantId,
                    'warehouse_id'       => $toId,
                    'original_quantity'  => $take,
                    'remaining_quantity' => $take,
                    'cost_price'         => $batch->cost_price,
                    'purchase_date'      => $batch->purchase_date,
                    'batch_code'         => $batch->batch_code ? $batch->batch_code . '-TR' : null,
                ]);

                $remainingToTransfer -= $take;
            }

            \App\Models\StockMovement::create([
                'type'               => 'warehouse_transfer',
                'product_id'         => $firstBatch->product_id ?? null,
                'product_variant_id' => $variantId,
                'from_warehouse_id'  => $fromId,
                'to_warehouse_id'    => $toId,
                'quantity'           => $quantityToTransfer,
                'user_id'            => auth()->id(),
            ]);

            return true;
        });
    }
}
