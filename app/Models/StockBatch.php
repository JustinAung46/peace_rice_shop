<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    protected $fillable = [
        'product_id', 
        'warehouse_id', 
        'original_quantity', 
        'remaining_quantity', 
        'cost_price', 
        'purchase_date', 
        'batch_code'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
