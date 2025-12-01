@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid">
    <h3 class="mb-4">Profit & Cash Flow Report</h3>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.profit') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card bg-success text-white mb-3 shadow-sm">
                <div class="card-body">
                    <h5>Total Income (Cash In)</h5>
                    <h3>{{ number_format($totalIncome, 2) }} Tk</h3>
                    <small>Sales: {{ $salesIncome }} + Due: {{ $dueCollection }}</small>
                </div>
            </div>

            <div class="card bg-danger text-white mb-3 shadow-sm">
                <div class="card-body">
                    <h5>Total Expense (Cash Out)</h5>
                    <h3>{{ number_format($totalExpense, 2) }} Tk</h3>
                    <small>Shop Cost & Bills</small>
                </div>
            </div>

            <div class="card {{ $netCash >= 0 ? 'bg-primary' : 'bg-warning text-dark' }} text-white shadow-sm">
                <div class="card-body">
                    <h5>Net Cash Balance</h5>
                    <h3>{{ number_format($netCash, 2) }} Tk</h3>
                    <small>Cash in Hand</small>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Income vs Expense Overview</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="width: 400px; height: 400px;">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(ctx, {
        type: 'doughnut', // bar
        data: {
            labels: ['Sales Income', 'Due Collection', 'Total Expense'],
            datasets: [{
                label: 'Amount (Tk)',
                data: [{{ $salesIncome }}, {{ $dueCollection }}, {{ $totalExpense }}],
                backgroundColor: [
                    '#198754', // Green for Sales
                    '#0dcaf0', // Cyan for Due
                    '#dc3545'  // Red for Expense
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
@endsection
