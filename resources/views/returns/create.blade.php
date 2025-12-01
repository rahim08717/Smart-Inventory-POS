@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Sales Return</h3>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('returns.search') }}" method="POST" class="row g-2 align-items-center">
                @csrf
                <div class="col-auto">
                    <label class="col-form-label fw-bold">Invoice No:</label>
                </div>
                <div class="col-auto">
                    <input type="text" name="invoice_no" class="form-control" placeholder="Ex: INV-173..." required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>

            @if(session('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
        </div>
    </div>

    @if(isset($order))
    <div class="card shadow-sm border-warning">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Return Items for Invoice: <strong>{{ $order->invoice_no }}</strong></h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Customer:</strong> {{ $order->customer->name ?? 'Walk-in' }}
                </div>
                <div class="col-md-4">
                    <strong>Date:</strong> {{ $order->created_at->format('d M Y') }}
                </div>
                <div class="col-md-4">
                    <strong>Total Paid:</strong> {{ $order->paid_amount }} Tk
                </div>
            </div>

            <form action="{{ route('returns.store') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product Name</th>
                            <th>Sold Qty</th>
                            <th>Unit Price</th>
                            <th width="150">Return Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit_price }}</td>
                            <td>
                                <input type="number"
                                       name="return_items[{{ $item->product_variant_id }}]"
                                       class="form-control"
                                       min="0"
                                       max="{{ $item->quantity }}"
                                       value="0">

                                <input type="hidden"
                                       name="prices[{{ $item->product_variant_id }}]"
                                       value="{{ $item->unit_price }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mb-3">
                    <label>Return Note / Reason:</label>
                    <textarea name="note" class="form-control" rows="2" placeholder="Ex: Defective product, Size mismatch"></textarea>
                </div>

                <button type="submit" class="btn btn-danger w-100">Process Return & Increase Stock</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
