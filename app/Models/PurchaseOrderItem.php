<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id', 'product_variant_id',
        'quantity_ordered', 'quantity_received', 'cost_price',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getQuantityRemainingAttribute(): int
    {
        return max(0, $this->quantity_ordered - $this->quantity_received);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->cost_price * $this->quantity_ordered;
    }
}
