@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4">Admin Dashboard</h3>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">Today's Sales</h6>
                    <h3 class="fw-bold">{{ number_format($todaySales, 2) }} Tk</h3>
                    <small>{{ $todayOrders }} Orders Today</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">Total Revenue</h6>
                    <h3 class="fw-bold">{{ number_format($totalSales, 2) }} Tk</h3>
                    <small>Lifetime Sales</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">Total Products</h6>
                    <h3 class="fw-bold">{{ $totalProducts }}</h3>
                    <small>Active Items</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">Customers</h6>
                    <h3 class="fw-bold">{{ $totalCustomers }}</h3>
                    <small>Registered Buyers</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Inv No</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->invoice_no }}</td>
                                <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                                <td class="fw-bold">{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $order->payment_method }}</span>
                                </td>
                                <td>{{ $order->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('orders.print', $order->id) }}" class="btn btn-sm btn-info text-white">Print</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No orders yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">⚠️ Low Stock Alert</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($lowStockItems as $stock)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $stock->variant->product->name ?? 'Unknown' }}</div>
                                    <small class="text-muted">
                                        {{ $stock->variant->variant_name }}
                                        ({{ $stock->warehouse->name ?? 'WH' }})
                                    </small>
                                </div>
                                <span class="badge bg-danger rounded-pill">{{ $stock->quantity }} Left</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-4 text-success">
                                <i class="bi bi-check-circle"></i> Stock is healthy!
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
