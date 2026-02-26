<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'unit_label',
        'selling_price',
        'pyi_per_bag',
        'price_per_pyi',
        'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
