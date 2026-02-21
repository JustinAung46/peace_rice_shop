<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'product_id', 'quantity', 'unit_price', 'cost_price', 'total_cost', 'subtotal', 'discount', 'total_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batches()
    {
        return $this->hasMany(SaleItemBatch::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

}
