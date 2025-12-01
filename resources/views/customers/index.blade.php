@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>{{ __('Customer Due List') }}</h3>


        @if(session('success'))
            <div class="alert alert-success py-1 px-3">{{ session('success') }}</div>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Total Purchase</th>
                        <th class="text-danger">Current Due</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ number_format($customer->orders->sum('total_amount'), 2) }}</td>

                        <td>
                            @if($customer->current_due > 0)
                                <span class="badge bg-danger fs-6">{{ number_format($customer->current_due, 2) }}</span>
                            @else
                                <span class="badge bg-success">Paid</span>
                            @endif
                        </td>

                        <td>
                            @if($customer->current_due > 0)
                                <button class="btn btn-sm btn-primary pay-btn"
                                    data-id="{{ $customer->id }}"
                                    data-name="{{ $customer->name }}"
                                    data-due="{{ $customer->current_due }}">
                                    Collect Due
                                </button>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>No Due</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="dueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Collect Due Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customers.payment') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="modalCustomerId">

                    <div class="mb-3">
                        <label>Customer Name</label>
                        <input type="text" id="modalCustomerName" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Current Due Amount</label>
                        <input type="text" id="modalDueAmount" class="form-control bg-light text-danger fw-bold" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Payment Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required min="1" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label>Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Note (Optional)</label>
                        <input type="text" name="note" class="form-control" placeholder="Ex: Paid via Bkash">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.pay-btn').click(function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let due = $(this).data('due');

            $('#modalCustomerId').val(id);
            $('#modalCustomerName').val(name);
            $('#modalDueAmount').val(due);

            var myModal = new bootstrap.Modal(document.getElementById('dueModal'));
            myModal.show();
        });
    });
</script>
@endsection
