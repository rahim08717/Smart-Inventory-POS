<?php

namespace App\Http\Controllers;
use App\Models\Expense;           
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {

        $startDate = $request->start_date ?? Carbon::today()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::today()->format('Y-m-d');


        $orders = Order::whereBetween('created_at', [
                        $startDate . ' 00:00:00',
                        $endDate . ' 23:59:59'
                    ])
                    ->with('customer')
                    ->latest()
                    ->get();


        $totalAmount = $orders->sum('total_amount');

        return view('reports.sales', compact('orders', 'startDate', 'endDate', 'totalAmount'));
    }

    public function profitReport(Request $request)
{
    $startDate = $request->start_date ?? Carbon::today()->format('Y-m-d');
    $endDate = $request->end_date ?? Carbon::today()->format('Y-m-d');


    $salesIncome = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->sum('paid_amount');


    $dueCollection = CustomerPayment::whereBetween('payment_date', [$startDate, $endDate])
                    ->sum('amount');


    $totalIncome = $salesIncome + $dueCollection;


    $totalExpense = Expense::whereBetween('expense_date', [$startDate, $endDate])
                    ->sum('amount');


    $netCash = $totalIncome - $totalExpense;

    return view('reports.profit', compact(
        'startDate',
        'endDate',
        'salesIncome',
        'dueCollection',
        'totalIncome',
        'totalExpense',
        'netCash'
    ));
}
}
