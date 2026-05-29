<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPayment extends Model
{
    protected $fillable = ['customer_id', 'amount', 'original_amount', 'note', 'updated_by'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function allocations()
    {
        return $this->hasMany(CreditAllocation::class);
    }

    public function logs()
    {
        return $this->hasMany(CreditPaymentLog::class);
    }
}
