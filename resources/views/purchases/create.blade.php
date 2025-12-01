@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4">Create Purchase (Stock In)</h3>

        <form id="purchaseForm">
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Purchase Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label>Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Warehouse <span class="text-danger">*</span></label>
                                <select name="warehouse_id" class="form-select" required>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Reference No (Optional)</label>
                                <input type="text" name="reference_no" class="form-control" placeholder="REF-001">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Add Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Select Product to Add</label>
                                <select id="productSelector" class="form-select">
                                    <option value="">Search and Select Product...</option>
                                    @foreach ($products as $variant)
                                        <option value="{{ $variant->id }}"
                                            data-name="{{ $variant->product->name }} - {{ $variant->variant_name }}"
                                            data-price="{{ $variant->price }}">
                                            {{ $variant->product->name }} - {{ $variant->variant_name }} (Current Price:
                                            {{ $variant->price }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <table class="table table-bordered table-hover" id="purchaseTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Product Name</th>
                                        <th width="20%">Quantity</th>
                                        <th width="20%">Unit Cost</th>
                                        <th width="15%">Subtotal</th>
                                        <th width="5%">X</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Grand Total</td>
                                        <td colspan="2">
                                            <input type="number" name="total_amount" id="grandTotal"
                                                class="form-control fw-bold" readonly value="0.00">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                            <button type="submit" class="btn btn-success w-100 mt-3 p-2">Submit Purchase</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script type="module">
        $(document).ready(function() {

            // 4. Submit Purchase Form via AJAX
            $('#purchaseForm').submit(function(e) {
                e.preventDefault();

                // Disable submit button to prevent double submission
                let submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).text('Processing...');

                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('purchases.store') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href =
                            "{{ route('products.index') }}"; // Redirect to product list or purchase list
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text('Submit Purchase');
                        alert('Something went wrong! Check console for details.');
                        console.log(xhr.responseText);
                    }
                });
            });

            // 1. Add Product to Table when selected from dropdown
            $('#productSelector').change(function() {
                let selectedOption = $(this).find(':selected');
                let variantId = $(this).val();
                let name = selectedOption.data('name');
                // We use 'price' as default cost, user can change it
                let cost = selectedOption.data('price');

                if (!variantId) return;

                // Check if product already exists in table
                if ($('#row-' + variantId).length > 0) {
                    alert('Product already added! Please increase quantity.');
                    $(this).val(''); // Reset dropdown
                    return;
                }

                let html = `
                <tr id="row-${variantId}">
                    <td>
                        ${name}
                        <input type="hidden" name="items[${variantId}][variant_id]" value="${variantId}">
                    </td>
                    <td>
                        <input type="number" name="items[${variantId}][quantity]" class="form-control qty-input" value="1" min="1">
                    </td>
                    <td>
                        <input type="number" name="items[${variantId}][unit_cost]" class="form-control cost-input" value="${cost}" step="0.01">
                    </td>
                    <td>
                        <span class="subtotal">${cost}</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">×</button>
                    </td>
                </tr>
            `;

                $('#purchaseTable tbody').append(html);
                $(this).val(''); // Reset dropdown
                calculateGrandTotal();
            });

            // 2. Remove Row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateGrandTotal();
            });

            // 3. Calculate Subtotal and Grand Total on Input Change
            $(document).on('input', '.qty-input, .cost-input', function() {
                let row = $(this).closest('tr');
                let qty = parseFloat(row.find('.qty-input').val()) || 0;
                let cost = parseFloat(row.find('.cost-input').val()) || 0;
                let subtotal = qty * cost;

                row.find('.subtotal').text(subtotal.toFixed(2));
                calculateGrandTotal();
            });

            // Function to Calculate Grand Total
            function calculateGrandTotal() {
                let total = 0;
                $('.subtotal').each(function() {
                    total += parseFloat($(this).text()) || 0;
                });
                $('#grandTotal').val(total.toFixed(2));
            }
        });
    </script>
@endsection
