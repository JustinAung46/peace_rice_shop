<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPayment extends Model
{
    protected $fillable = ['customer_id', 'amount', 'note'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
