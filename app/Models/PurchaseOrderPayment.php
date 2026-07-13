<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderPayment extends Model
{
    protected $fillable = ['purchase_order_id', 'amount', 'payment_date', 'note', 'paid_by'];

    protected $casts = ['payment_date' => 'date'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
