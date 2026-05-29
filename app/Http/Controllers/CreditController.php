<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CreditPayment;
use App\Models\CreditPaymentLog;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    /**
     * Credit Report - list all credit transactions with filters.
     */
    public function index(Request $request)
    {
        $customers = Customer::orderBy('name')->get();

        // Build query for credit sales (sales that have a Credit payment)
        $query = Sale::with(['customer', 'payments', 'items'])
            ->where('status', '!=', 'cancelled')
            ->whereHas('payments', function ($q) {
                $q->where('payment_method', 'Credit');
            });

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $creditSales = $query->orderByDesc('created_at')->get();

        // Customers that have credit balance > 0 for summary cards
        $customersWithBalance = Customer::where('credit_balance', '>', 0)->orderByDesc('credit_balance')->get();

        return view('credits.index', compact('creditSales', 'customers', 'customersWithBalance'));
    }

    /**
     * Full credit history for a specific customer.
     */
    public function history(Customer $customer)
    {
        // All credit sales for this customer
        $creditSales = Sale::with(['items.variant.product', 'payments'])
            ->where('customer_id', $customer->id)
            ->where('status', '!=', 'cancelled')
            ->whereHas('payments', function ($q) {
                $q->where('payment_method', 'Credit');
            })
            ->orderBy('created_at')
            ->get();

        // All credit payments from this customer
        $creditPayments = CreditPayment::with(['allocations.sale'])
            ->where('customer_id', $customer->id)
            ->orderBy('created_at')
            ->get();

        // Merge into a single timeline sorted by date
        $timeline = collect();

        foreach ($creditSales as $sale) {
            $creditAmount = $sale->payments->where('payment_method', 'Credit')->sum('amount');
            $timeline->push([
                'type'   => 'sale',
                'date'   => $sale->created_at,
                'data'   => $sale,
                'amount' => $creditAmount,
            ]);
        }

        foreach ($creditPayments as $payment) {
            $timeline->push([
                'type'   => 'payment',
                'date'   => $payment->created_at,
                'data'   => $payment,
                'amount' => $payment->amount,
            ]);
        }

        $timeline = $timeline->sortBy('date')->values();

        return view('credits.history', compact('customer', 'timeline', 'creditSales'));
    }

    /**
     * Record a credit payment from a customer.
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|integer|min:1',
            'note'        => 'nullable|string|max:500',
            'sale_id'     => 'nullable|exists:sales,id',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        if ($validated['amount'] > $customer->credit_balance) {
            return back()->withErrors(['amount' => 'Payment exceeds outstanding credit balance of ' . number_format($customer->credit_balance) . ' Ks.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $payment = CreditPayment::create([
                'customer_id'     => $validated['customer_id'],
                'amount'          => $validated['amount'],
                'original_amount' => $validated['amount'], // Store original — never changes
                'note'            => $validated['note'] ?? null,
            ]);

            $customer->decrement('credit_balance', $validated['amount']);

            // FIFO or Specific Allocation Logic
            $remainingPayment = $validated['amount'];

            $query = Sale::where('customer_id', $customer->id)
                ->where('credit_remaining', '>', 0)
                ->lockForUpdate();

            if (!empty($validated['sale_id'])) {
                $query->where('id', $validated['sale_id']);
            } else {
                $query->orderBy('created_at', 'asc');
            }

            $unpaidSales = $query->get();

            foreach ($unpaidSales as $sale) {
                if ($remainingPayment <= 0) break;

                $allocationAmount = min($sale->credit_remaining, $remainingPayment);

                \App\Models\CreditAllocation::create([
                    'credit_payment_id' => $payment->id,
                    'sale_id'           => $sale->id,
                    'amount'            => $allocationAmount,
                ]);

                $sale->credit_remaining -= $allocationAmount;

                if ($sale->credit_remaining == 0) {
                    $sale->payment_status = 'paid';
                }

                $sale->save();

                $remainingPayment -= $allocationAmount;
            }

            // Audit log: created
            CreditPaymentLog::create([
                'credit_payment_id' => $payment->id,
                'customer_id'       => $customer->id,
                'action'            => 'created',
                'old_amount'        => null,
                'new_amount'        => $payment->amount,
                'old_note'          => null,
                'new_note'          => $payment->note,
                'performed_by'      => Auth::id(),
                'ip_address'        => $request->ip(),
                'created_at'        => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['amount' => 'Failed to record payment: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('credits.index')
            ->with('success', 'Payment of ' . number_format($validated['amount']) . ' Ks recorded for ' . $customer->name . '.');
    }

    /**
     * Update (edit) an existing credit payment — Admin only.
     */
    public function updatePayment(Request $request, CreditPayment $payment)
    {
        // Authorization handled by can:admin middleware on this route
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'note'   => 'nullable|string|max:500',
        ]);

        $customer = Customer::findOrFail($payment->customer_id);

        // Max allowed new amount = what the customer actually owes + old payment (since old payment was already deducted)
        $maxAllowed = $customer->credit_balance + $payment->amount;
        if ($validated['amount'] > $maxAllowed) {
            return back()->withErrors(['amount' => 'Amount exceeds the customer\'s outstanding credit. Maximum allowed: ' . number_format($maxAllowed) . ' Ks.'])->withInput();
        }

        // Snapshot for audit log
        $oldAmount = $payment->amount;
        $oldNote   = $payment->note;

        DB::beginTransaction();
        try {
            // Step 1: Reverse all existing allocations for this payment
            $allocations = $payment->allocations()->lockForUpdate()->get();

            foreach ($allocations as $alloc) {
                $sale = Sale::lockForUpdate()->find($alloc->sale_id);
                if ($sale) {
                    $sale->credit_remaining += $alloc->amount;
                    // Revert payment_status if it was paid
                    if ($sale->payment_status === 'paid' && $sale->credit_remaining > 0) {
                        $sale->payment_status = $sale->credit_remaining < $sale->total_amount ? 'partial' : 'unpaid';
                    }
                    $sale->save();
                }
            }

            // Step 2: Restore the old amount to customer balance
            $customer->increment('credit_balance', $oldAmount);

            // Step 3: Delete old allocations
            $payment->allocations()->delete();

            // Step 4: Update the payment record
            $payment->update([
                'amount'     => $validated['amount'],
                'note'       => $validated['note'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            // Step 5: Re-apply FIFO allocations with new amount
            $remainingPayment = $validated['amount'];

            $unpaidSales = Sale::where('customer_id', $customer->id)
                ->where('credit_remaining', '>', 0)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($unpaidSales as $sale) {
                if ($remainingPayment <= 0) break;

                $allocationAmount = min($sale->credit_remaining, $remainingPayment);

                \App\Models\CreditAllocation::create([
                    'credit_payment_id' => $payment->id,
                    'sale_id'           => $sale->id,
                    'amount'            => $allocationAmount,
                ]);

                $sale->credit_remaining -= $allocationAmount;

                if ($sale->credit_remaining == 0) {
                    $sale->payment_status = 'paid';
                }

                $sale->save();

                $remainingPayment -= $allocationAmount;
            }

            // Step 6: Deduct new amount from customer balance
            $customer->decrement('credit_balance', $validated['amount']);

            // Audit log: edited
            CreditPaymentLog::create([
                'credit_payment_id' => $payment->id,
                'customer_id'       => $customer->id,
                'action'            => 'edited',
                'old_amount'        => $oldAmount,
                'new_amount'        => $validated['amount'],
                'old_note'          => $oldNote,
                'new_note'          => $validated['note'] ?? null,
                'performed_by'      => Auth::id(),
                'ip_address'        => $request->ip(),
                'created_at'        => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['amount' => 'Failed to update payment: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('credits.history', $customer)
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Delete a credit payment and restore all balances — Admin only.
     */
    public function destroyPayment(Request $request, CreditPayment $payment)
    {
        // Authorization handled by can:admin middleware on this route
        $customer  = Customer::findOrFail($payment->customer_id);
        $oldAmount = $payment->amount;
        $oldNote   = $payment->note;

        DB::beginTransaction();
        try {
            // Step 1: Reverse all existing allocations
            $allocations = $payment->allocations()->lockForUpdate()->get();

            foreach ($allocations as $alloc) {
                $sale = Sale::lockForUpdate()->find($alloc->sale_id);
                if ($sale) {
                    $sale->credit_remaining += $alloc->amount;
                    if ($sale->payment_status === 'paid' && $sale->credit_remaining > 0) {
                        $sale->payment_status = $sale->credit_remaining < $sale->total_amount ? 'partial' : 'unpaid';
                    }
                    $sale->save();
                }
            }

            // Step 2: Restore balance to customer
            $customer->increment('credit_balance', $oldAmount);

            // Step 3: Delete allocations
            $payment->allocations()->delete();

            // Audit log: deleted (before deleting the payment row itself)
            CreditPaymentLog::create([
                'credit_payment_id' => null, // Payment row will be gone
                'customer_id'       => $customer->id,
                'action'            => 'deleted',
                'old_amount'        => $oldAmount,
                'new_amount'        => null,
                'old_note'          => $oldNote,
                'new_note'          => null,
                'performed_by'      => Auth::id(),
                'ip_address'        => $request->ip(),
                'created_at'        => now(),
            ]);

            // Step 4: Delete the payment itself
            $payment->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete payment: ' . $e->getMessage()]);
        }

        return redirect()->route('credits.history', $customer)
            ->with('success', 'Payment of ' . number_format($oldAmount) . ' Ks has been deleted and the customer\'s balance has been restored.');
    }

    /**
     * Show the full audit log for a customer's credit payments — Admin only.
     */
    public function auditLog(Customer $customer)
    {
        // Authorization handled by can:admin middleware on this route
        $logs = CreditPaymentLog::with(['performer'])
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('credits.audit', compact('customer', 'logs'));
    }
}
