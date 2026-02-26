<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CreditPayment;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Http\Request;
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
            ->whereHas('payments', function ($q) {
                $q->where('payment_method', 'Credit');
            })
            ->orderBy('created_at')
            ->get();

        // All credit payments from this customer
        $creditPayments = CreditPayment::where('customer_id', $customer->id)
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

        return view('credits.history', compact('customer', 'timeline'));
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
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        if ($validated['amount'] > $customer->credit_balance) {
            return back()->withErrors(['amount' => 'Payment exceeds outstanding credit balance of ' . number_format($customer->credit_balance) . ' Ks.'])->withInput();
        }

        DB::beginTransaction();
        try {
            CreditPayment::create($validated);

            $customer->decrement('credit_balance', $validated['amount']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['amount' => 'Failed to record payment: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('credits.index')
            ->with('success', 'Payment of ' . number_format($validated['amount']) . ' Ks recorded for ' . $customer->name . '.');
    }
}
