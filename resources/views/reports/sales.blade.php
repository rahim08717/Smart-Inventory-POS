@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Sales Report</h3>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter Report
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Report Results</h5>
            <h5 class="fw-bold text-success mb-0">Total Sales: {{ number_format($totalAmount, 2) }} Tk</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Items Qty</th>
                        <th>Total Amount</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $order->invoice_no }}</td>
                        <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ $order->items->count() }} Items</td>
                        <td class="fw-bold">{{ number_format($order->total_amount, 2) }}</td>
                        <td class="text-success">{{ number_format($order->paid_amount, 2) }}</td>
                        <td class="text-danger">
                            @if($order->due_amount > 0)
                                {{ number_format($order->due_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('orders.print', $order->id) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                View Invoice
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            No sales found for the selected date range.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
