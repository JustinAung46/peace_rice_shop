<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderReceiptItem extends Model
{
    protected $fillable = [
        'purchase_order_receipt_id', 'purchase_order_item_id', 'warehouse_id', 'quantity',
    ];

    public function receipt()
    {
        return $this->belongsTo(PurchaseOrderReceipt::class, 'purchase_order_receipt_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
