<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id', 'order_number', 'order_date', 'expected_date',
        'notes', 'total_cost', 'amount_paid', 'payment_status', 'receive_status',
        'created_by',
    ];

    protected $casts = [
        'order_date'    => 'date',
        'expected_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receipts()
    {
        return $this->hasMany(PurchaseOrderReceipt::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchaseOrderPayment::class);
    }

    // ── Computed helpers ──────────────────────────────────────────────────────

    public function getAmountRemainingAttribute(): int
    {
        return max(0, $this->total_cost - $this->amount_paid);
    }

    public function getPaymentPercentAttribute(): int
    {
        if ($this->total_cost <= 0) return 0;
        return (int) round(($this->amount_paid / $this->total_cost) * 100);
    }

    // ── Helpers for status recalculation ─────────────────────────────────────

    public function recalculatePaymentStatus(): void
    {
        if ($this->amount_paid <= 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->amount_paid >= $this->total_cost) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partial';
        }
    }

    public function recalculateReceiveStatus(): void
    {
        $this->loadMissing('items');
        $totalOrdered  = $this->items->sum('quantity_ordered');
        $totalReceived = $this->items->sum('quantity_received');

        if ($totalReceived <= 0) {
            $this->receive_status = 'pending';
        } elseif ($totalReceived >= $totalOrdered) {
            $this->receive_status = 'received';
        } else {
            $this->receive_status = 'partial';
        }
    }

    // ── Auto-generate order number ────────────────────────────────────────────

    public static function generateOrderNumber(): string
    {
        $prefix = 'PO-' . now()->format('Ymd') . '-';
        $last = self::where('order_number', 'like', $prefix . '%')
            ->orderByDesc('order_number')
            ->value('order_number');

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
