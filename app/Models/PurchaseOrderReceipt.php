<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderReceipt extends Model
{
    protected $fillable = ['purchase_order_id', 'received_date', 'notes', 'received_by'];

    protected $casts = ['received_date' => 'date'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receiptItems()
    {
        return $this->hasMany(PurchaseOrderReceiptItem::class, 'purchase_order_receipt_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
