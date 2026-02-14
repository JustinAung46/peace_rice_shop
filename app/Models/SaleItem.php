<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'product_id', 'stock_batch_id', 'quantity', 'unit_price', 'cost_price', 'subtotal', 'discount', 'total_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockBatch()
    {
        return $this->belongsTo(StockBatch::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

}
