<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPaymentLog extends Model
{
    public $timestamps = false; // Only has created_at, managed manually

    protected $fillable = [
        'credit_payment_id',
        'customer_id',
        'action',
        'old_amount',
        'new_amount',
        'old_note',
        'new_note',
        'performed_by',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(CreditPayment::class, 'credit_payment_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
