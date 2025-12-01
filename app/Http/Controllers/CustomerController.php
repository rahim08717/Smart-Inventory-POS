<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerPayment;

class CustomerController extends Controller
{
    public function index()
    {

        $customers = Customer::with(['orders', 'payments'])->get();


        $customers->map(function($customer) {
            $totalPurchase = $customer->orders->sum('total_amount');
            $paidInOrders = $customer->orders->sum('paid_amount');
            $collectedDue = $customer->payments->sum('amount');

            $totalPaid = $paidInOrders + $collectedDue;
            $customer->current_due = $totalPurchase - $totalPaid;

            return $customer;
        });

        return view('customers.index', compact('customers'));
    }

    
    public function storePayment(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        CustomerPayment::create([
            'customer_id' => $request->customer_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'note' => $request->note
        ]);

        return redirect()->back()->with('success', 'Payment Collected Successfully!');
    }
}
