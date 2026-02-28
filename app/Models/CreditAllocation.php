<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditAllocation extends Model
{
    protected $fillable = ['credit_payment_id', 'sale_id', 'amount'];

    public function creditPayment()
    {
        return $this->belongsTo(CreditPayment::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
