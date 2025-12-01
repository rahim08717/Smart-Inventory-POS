<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Customer;
use Carbon\Carbon; 

class DashboardController extends Controller
{
    public function index()
    {

        $today = Carbon::today();

        $todaySales = Order::whereDate('created_at', $today)->sum('total_amount');
        $todayOrders = Order::whereDate('created_at', $today)->count();


        $totalSales = Order::sum('total_amount');
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        $recentOrders = Order::with('customer')->latest()->take(5)->get();


        $lowStockItems = \App\Models\Stock::where('quantity', '<=', 5)
                            ->with(['variant.product', 'warehouse'])
                            ->get();

        return view('dashboard', compact(
            'todaySales',
            'todayOrders',
            'totalSales',
            'totalProducts',
            'totalCustomers',
            'recentOrders',
            'lowStockItems'
        ));
    }
}
