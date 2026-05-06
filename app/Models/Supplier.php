<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'notes'];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function getTotalOutstandingAttribute(): int
    {
        return $this->purchaseOrders()
            ->where('payment_status', '!=', 'paid')
            ->get()
            ->sum(fn($o) => $o->total_cost - $o->amount_paid);
    }
}
