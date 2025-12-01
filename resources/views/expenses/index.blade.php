@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="bi bi-wallet2"></i> {{ __('Expenses') }}</h3>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-gradient bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle"></i> {{ __('Add New Expense') }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Category') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-tags"></i></span>
                                <select name="category" class="form-select border-start-0" required>
                                    <option value="Shop Rent">{{ __('Shop Rent') }}</option>
                                    <option value="Electricity Bill">{{ __('Electricity Bill') }}</option>
                                    <option value="Staff Salary">{{ __('Staff Salary') }}</option>
                                    <option value="Tea/Snacks">{{ __('Tea/Snacks') }}</option>
                                    <option value="Internet">{{ __('Internet') }}</option>
                                    <option value="Other">{{ __('Other') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Amount') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-cash"></i></span>
                                <input type="number" name="amount" class="form-control border-start-0" placeholder="0.00" required>

                                <button type="button" class="btn btn-outline-secondary border-start-0 voice-btn" title="{{ __('Speak Amount') }}">
                                    <i class="bi bi-mic-fill text-primary"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Date') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar3"></i></span>
                                <input type="date" name="expense_date" class="form-control border-start-0" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ __('Note (Optional)') }}</label>
                            <div class="input-group">
                                <textarea name="note" class="form-control border-end-0" rows="3" placeholder="{{ __('Enter expense details...') }}"></textarea>

                                <button type="button" class="btn btn-outline-secondary border-start-0 voice-btn" title="{{ __('Speak Note') }}">
                                    <i class="bi bi-mic-fill text-primary"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill shadow-sm fw-bold">
                                <i class="bi bi-check-lg"></i> {{ __('Save Expense') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold text-secondary"><i class="bi bi-list-ul"></i> {{ __('Expense List') }}</h5>
                    <div class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fs-6 border border-danger shadow-sm">
                        {{ __('Total') }}: {{ number_format($expenses->sum('amount'), 2) }} Tk
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary text-uppercase small fw-bold">
                                <tr>
                                    <th class="py-3 ps-4">{{ __('Date') }}</th>
                                    <th class="py-3">{{ __('Category') }}</th>
                                    <th class="py-3">{{ __('Note') }}</th>
                                    <th class="py-3 text-end">{{ __('Amount') }}</th>
                                    <th class="py-3 text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                <tr class="border-bottom">
                                    <td class="ps-4 text-muted fw-medium">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary rounded-pill px-3 py-1">
                                            {{ $expense->category }}
                                        </span>
                                    </td>
                                    <td class="small text-secondary">{{ $expense->note ?? '-' }}</td>
                                    <td class="text-end fw-bold text-dark fs-6">{{ number_format($expense->amount, 2) }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this expense?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" title="{{ __('Delete') }}" style="width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-25"></i>
                                        {{ __('No expenses recorded.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // জেনেরিক ভয়েস ফাংশন (যেকোনো ইনপুটে কাজ করবে)
        document.querySelectorAll('.voice-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // ব্রাউজার সাপোর্ট চেক
                if (!('webkitSpeechRecognition' in window)) {
                    alert("Your browser doesn't support Voice Input. Try Google Chrome.");
                    return;
                }

                let icon = this.querySelector('i');
                let inputField = this.parentElement.querySelector('input, textarea');

                const recognition = new webkitSpeechRecognition();
                recognition.lang = 'en-US'; // বাংলা চাইলে 'bn-BD' দিন
                recognition.continuous = false;
                recognition.interimResults = false;

                // মাইক অন হলে
                recognition.onstart = function() {
                    icon.classList.remove('bi-mic-fill', 'text-primary');
                    icon.classList.add('bi-mic-mute-fill', 'text-danger', 'spinner-grow', 'spinner-grow-sm');
                    inputField.placeholder = "Listening...";
                };

                // রেজাল্ট পেলে
                recognition.onresult = function(event) {
                    const transcript = event.results[0][0].transcript;

                    // যদি ইনপুট টাইপ নম্বর হয় (Amount ফিল্ড)
                    if(inputField.type === 'number') {
                        // টেক্সট থেকে শুধু নাম্বার বের করা (যেমন: "Tk 500" -> 500)
                        inputField.value = transcript.replace(/[^0-9.]/g, '');
                    } else {
                        // টেক্সট ফিল্ড (Note)
                        inputField.value = transcript;
                    }
                };

                // মাইক বন্ধ হলে
                recognition.onend = function() {
                    icon.classList.remove('bi-mic-mute-fill', 'text-danger', 'spinner-grow', 'spinner-grow-sm');
                    icon.classList.add('bi-mic-fill', 'text-primary');
                    inputField.placeholder = "";
                };

                // এরর হলে
                recognition.onerror = function(event) {
                    console.error("Voice Error:", event.error);
                    icon.classList.remove('bi-mic-mute-fill', 'text-danger', 'spinner-grow', 'spinner-grow-sm');
                    icon.classList.add('bi-mic-fill', 'text-primary');
                    alert("Could not hear properly. Try again.");
                };

                recognition.start();
            });
        });
    });
</script>
@endsection
