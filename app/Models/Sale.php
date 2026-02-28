<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['invoice_number', 'total_amount', 'payment_method', 'customer_id', 'status', 'payment_status', 'sale_type', 'credit_remaining'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function creditAllocations()
    {
        return $this->hasMany(CreditAllocation::class);
    }
}
