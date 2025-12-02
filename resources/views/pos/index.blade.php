@extends('layouts.app')

@section('content')
    <style>
        /* POS Layout */
        .pos-container { height: calc(100vh - 100px); overflow: hidden; }

        /* Left Side (Products) */
        .product-section { height: 100%; overflow-y: auto; padding-right: 10px; }

        /* Product Card Styling */
        .product-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: var(--bs-body-bg);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden; /* Image overflow fix */
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            border: 1px solid var(--bs-primary);
            cursor: pointer;
        }

        /* Image Section Updated */
        .product-img-container {
            height: 120px; /* Height increased slightly */
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-bottom: 1px solid #f0f0f0;
        }
        .product-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Image will cover the area */
        }
        .placeholder-text {
            font-size: 2rem;
            color: #adb5bd;
            font-weight: bold;
        }

        /* Right Side (Cart) */
        .cart-section {
            height: 100%;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            background: var(--bs-body-bg);
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            border: 1px solid var(--bs-border-color);
        }
        .cart-items { flex-grow: 1; overflow-y: auto; scrollbar-width: thin; }

        /* Responsive Tweaks */
        @media (max-width: 768px) {
            .pos-container { height: auto; overflow: visible; }
            .product-section { height: auto; padding-right: 0; margin-bottom: 20px; }
            .cart-section { height: 500px; }
        }

        /* Voice Animation */
        .listening-animation {
            animation: pulse-red 1.5s infinite;
        }
        @keyframes pulse-red {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
    </style>

    <div class="row pos-container">
        <div class="col-md-7 col-lg-8 product-section">

            <div class="card border-0 shadow-sm mb-3 rounded-pill overflow-hidden">
                <div class="card-body p-1 ps-3 d-flex align-items-center bg-body">
                    <i class="bi bi-search text-muted me-2"></i>
                    <input type="text" id="searchProduct" class="form-control border-0 shadow-none bg-transparent"
                        placeholder="{{ __('Scan Barcode or Search Product...') }}">

                    <button class="btn btn-light rounded-circle ms-2" type="button" id="voiceBtn"
                            title="{{ __('Speak to Search') }}" style="width: 45px; height: 45px;">
                        <i class="bi bi-mic-fill text-primary" id="micIcon"></i>
                    </button>
                </div>
            </div>

            <div class="row g-3" id="productGrid">
                @forelse($products as $product)
                    @foreach ($product->variants as $variant)
                        @php
                            $totalStock = $variant->stock->sum('quantity');
                        @endphp

                        <div class="col-6 col-md-4 col-lg-3 product-item"
                            data-name="{{ $product->name }} ({{ $variant->variant_name }})">
                            <div class="card product-card h-100"
                                onclick="addToCart({{ $variant->id }}, '{{ $product->name }} - {{ $variant->variant_name }}', {{ $variant->price }}, {{ $totalStock }})">

                                <div class="product-img-container">
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="placeholder-text">
                                            {{ substr($product->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="card-body p-2 text-center">
                                    <h6 class="card-title mb-1 text-truncate small fw-bold" title="{{ $product->name }}">
                                        {{ $product->name }}</h6>
                                    <span class="badge bg-light text-dark border mb-2">{{ $variant->variant_name }}</span>

                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <span class="fw-bold text-primary small">{{ $variant->price }} Tk</span>
                                        @if ($totalStock > 0)
                                            <span class="badge bg-success-subtle text-success border border-success rounded-pill" style="font-size: 0.6rem">{{ $totalStock }} {{ __('Left') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill" style="font-size: 0.6rem">{{ __('Out') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="col-12 text-center mt-5 text-muted">
                        <i class="bi bi-box-seam fs-1"></i>
                        <p>{{ __('No products found.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="cart-section">
                <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center rounded-top-4">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-cart3"></i> {{ __('Current Order') }}</h5>
                    <span class="badge bg-primary rounded-pill" id="itemCount">0 {{ __('Items') }}</span>
                </div>

                <div class="cart-items p-0">
                    <table class="table table-hover mb-0" id="cartTable">
                        <thead class="table-light sticky-top small">
                            <tr>
                                <th width="40%">{{ __('Product') }}</th>
                                <th width="25%" class="text-center">{{ __('Qty') }}</th>
                                <th width="20%" class="text-end">{{ __('Total') }}</th>
                                <th width="15%" class="text-center">X</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="emptyCartMsg">
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-basket3 display-4 text-secondary opacity-50"></i> <br>
                                    <span class="small">{{ __('Cart is Empty') }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top bg-body">
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>{{ __('Subtotal') }}:</span>
                        <span class="fw-bold text-dark" id="subTotal">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>{{ __('Tax') }} (0%):</span>
                        <span class="fw-bold text-dark">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pt-2 border-top">
                        <h4 class="fw-bold text-dark">{{ __('Total') }}:</h4>
                        <h4 class="fw-bold text-primary" id="grandTotal">0.00</h4>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-danger w-100 rounded-pill" onclick="clearCart()">
                                <i class="bi bi-trash"></i> {{ __('Cancel') }}
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm" onclick="openPaymentModal()">
                                <i class="bi bi-credit-card"></i> {{ __('Pay Now') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-wallet2"></i> {{ __('Finalize Payment') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="orderForm">
                        <input type="hidden" name="subtotal" id="modalSubtotal">

                        <div class="mb-3">
                            <label class="fw-bold small text-muted">{{ __('Customer') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                <select name="customer_id" id="customerSelect" class="form-select border-start-0" required>
                                    <option value="" data-points="0">{{ __('Select Customer') }}</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" data-points="{{ $customer->total_points ?? 0 }}">
                                            {{ $customer->name }} ({{ $customer->phone }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-muted">{{ __('Total Amount') }}</span>
                                    <input type="text" name="total_amount" id="modalTotal"
                                           class="form-control-plaintext text-end fw-bold fs-4 text-primary p-0" readonly value="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="card border-info mb-3 bg-info-subtle">
                            <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                <span class="small text-info-emphasis"><i class="bi bi-star-fill text-warning"></i> {{ __('Available Points') }}: <strong id="availPoints">0</strong></span>
                                <span class="badge bg-white text-info border border-info small">1 Pt = 1 Tk</span>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="small fw-bold text-muted">{{ __('Redeem Points') }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="redeem_points" id="redeemPoints" class="form-control" value="0" min="0">
                                    <button type="button" class="btn btn-outline-primary" id="applyPointsBtn">{{ __('Apply') }}</button>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted">{{ __('Points Discount') }}</label>
                                <input type="number" name="points_discount" id="pointsDiscount" class="form-control form-control-sm bg-light" value="0" readonly>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="small fw-bold text-muted">{{ __('Manual Discount') }}</label>
                                <input type="number" name="discount" id="modalDiscount" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted">{{ __('Payment Method') }}</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Bkash">Bkash/Mobile Banking</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <label class="fw-bold text-success">{{ __('Paid Amount') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white"><i class="bi bi-cash"></i></span>
                                    <input type="number" name="paid_amount" id="modalPaid" class="form-control border-success" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="fw-bold text-danger">{{ __('Change/Return') }}</label>
                                <input type="text" id="modalChange" class="form-control bg-light text-danger fw-bold" readonly value="0.00">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                            <i class="bi bi-check-circle-fill"></i> {{ __('Complete Order') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        $(document).ready(function() {
            let cart = [];

            // --- Voice Search ---
            $('#voiceBtn').click(function() {
                if (!('webkitSpeechRecognition' in window)) {
                    alert("Your browser doesn't support Voice Search. Try Google Chrome.");
                    return;
                }

                const recognition = new webkitSpeechRecognition();
                recognition.lang = 'en-US';
                recognition.continuous = false;
                recognition.interimResults = false;

                recognition.onstart = function() {
                    $('#micIcon').removeClass('text-primary bi-mic-fill').addClass('text-danger bi-mic-mute-fill listening-animation');
                    $('#searchProduct').attr('placeholder', 'Listening... Speak now!');
                };

                recognition.onresult = function(event) {
                    const transcript = event.results[0][0].transcript;
                    $('#searchProduct').val(transcript);
                    filterProducts(transcript.toLowerCase());
                };

                recognition.onend = function() {
                    $('#micIcon').removeClass('text-danger bi-mic-mute-fill listening-animation').addClass('text-primary bi-mic-fill');
                    $('#searchProduct').attr('placeholder', "{{ __('Scan Barcode or Search Product...') }}");
                };

                recognition.onerror = function(event) {
                    if(event.error !== 'no-speech') {
                        console.error("Voice Error:", event.error);
                    }
                    $('#micIcon').removeClass('text-danger bi-mic-mute-fill listening-animation').addClass('text-primary bi-mic-fill');
                };

                recognition.start();
            });

            // --- Product Filtering ---
            $('#searchProduct').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                filterProducts(value);
            });

            function filterProducts(value) {
                document.querySelectorAll('.product-item').forEach(function(item) {
                    let name = item.getAttribute('data-name').toLowerCase();
                    item.style.display = name.includes(value) ? "block" : "none";
                });
            }

            // --- Cart Logic ---
            window.addToCart = function(id, name, price, stock) {
                if (stock <= 0) { alert("Stock Out!"); return; }

                let existingItem = cart.find(item => item.id === id);
                if (existingItem) {
                    if (existingItem.qty < stock) existingItem.qty++;
                    else alert("Stock limit reached!");
                } else {
                    cart.push({ id: id, name: name, price: parseFloat(price), qty: 1, stock: stock });
                }
                renderCart();
            };

            function renderCart() {
                let tbody = $('#cartTable tbody');
                tbody.empty();
                let subTotal = 0;
                let count = 0;

                if (cart.length === 0) {
                    tbody.html(`<tr id="emptyCartMsg"><td colspan="4" class="text-center py-5 text-muted"><i class="bi bi-basket3 display-4 text-secondary opacity-50"></i><br><span class="small">{{ __('Cart is Empty') }}</span></td></tr>`);
                    updateTotals(0, 0);
                    return;
                }

                cart.forEach((item, index) => {
                    let total = item.price * item.qty;
                    subTotal += total;
                    count += item.qty;
                    tbody.append(`
                        <tr>
                            <td class="align-middle"><div class="text-truncate" style="max-width: 120px;" title="${item.name}">${item.name}</div><small class="text-muted">${item.price}</small></td>
                            <td class="align-middle text-center">
                                <div class="btn-group btn-group-sm rounded-pill" role="group">
                                    <button class="btn btn-outline-secondary py-0" onclick="updateQty(${index}, -1)">-</button>
                                    <button class="btn btn-outline-secondary py-0 disabled text-dark fw-bold" style="width:30px">${item.qty}</button>
                                    <button class="btn btn-outline-secondary py-0" onclick="updateQty(${index}, 1)">+</button>
                                </div>
                            </td>
                            <td class="align-middle text-end fw-bold">${total.toFixed(2)}</td>
                            <td class="align-middle text-center"><button class="btn btn-link text-danger p-0" onclick="removeFromCart(${index})"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    `);
                });
                updateTotals(subTotal, count);
            }

            function updateTotals(total, count) {
                $('#subTotal').text(total.toFixed(2));
                $('#grandTotal').text(total.toFixed(2));
                $('#itemCount').text(count + " {{ __('Items') }}");
            }

            window.updateQty = function(index, change) {
                let item = cart[index];
                if (change === -1 && item.qty > 1) item.qty--;
                else if (change === 1) {
                    if (item.qty < item.stock) item.qty++;
                    else alert("Limit reached!");
                }
                renderCart();
            };

            window.removeFromCart = function(index) {
                cart.splice(index, 1);
                renderCart();
            };

            window.clearCart = function() {
                if (confirm("Clear cart?")) { cart = []; renderCart(); }
            };

            // --- Payment & Barcode Logic ---
            window.openPaymentModal = function() {
                if (cart.length === 0) { alert("Cart is empty!"); return; }
                let total = parseFloat($('#grandTotal').text());
                $('#modalSubtotal').val(total);
                $('#modalTotal').val(total.toFixed(2));
                $('#modalPaid').val(total);
                $('#modalDiscount').val(0);
                $('#redeemPoints').val(0);
                $('#pointsDiscount').val(0);
                $('#modalChange').val(0);
                $('#customerSelect').val('').change();
                new bootstrap.Modal('#paymentModal').show();
            };

            // Barcode
            let barcodeBuffer = ""; let barcodeTimeout;
            $(document).on('keypress', function(e) {
                if ($(e.target).is('input, textarea')) return;
                if (barcodeTimeout) clearTimeout(barcodeTimeout);
                if (e.which === 13) {
                    if (barcodeBuffer.length > 0) { handleBarcodeScan(barcodeBuffer); barcodeBuffer = ""; }
                } else { barcodeBuffer += String.fromCharCode(e.which); }
                barcodeTimeout = setTimeout(() => { barcodeBuffer = ""; }, 500);
            });

            function handleBarcodeScan(code) {
                $.ajax({
                    url: '/products/search-by-barcode',
                    method: 'GET',
                    data: { barcode: code },
                    success: function(p) {
                        if (p) { addToCart(p.id, p.name + ' - ' + p.variant_name, p.price, p.stock); $('#searchProduct').val(''); }
                        else { alert('Not Found!'); }
                    }
                });
            }

            // Calculation Logic inside Modal
            $('#customerSelect').change(function() {
                let pts = $(this).find(':selected').data('points') || 0;
                $('#availPoints').text(pts);
                $('#redeemPoints').attr('max', pts);
            });

            $('#applyPointsBtn').click(function(e) {
                e.preventDefault();
                let pts = parseFloat($('#redeemPoints').val()) || 0;
                let avail = parseFloat($('#availPoints').text());
                let sub = parseFloat($('#modalSubtotal').val());
                if (pts > avail) { alert("Not enough points!"); return; }
                if (pts > sub) { alert("Discount exceeds total!"); return; }
                $('#pointsDiscount').val(pts);
                calcFinal();
            });

            $('#modalDiscount, #modalPaid').on('input', function() { calcFinal(); });

            function calcFinal() {
                let sub = parseFloat($('#modalSubtotal').val()) || 0;
                let disc = parseFloat($('#modalDiscount').val()) || 0;
                let pts = parseFloat($('#pointsDiscount').val()) || 0;
                let paid = parseFloat($('#modalPaid').val()) || 0;
                let total = sub - disc - pts;
                if(total < 0) total = 0;
                $('#modalTotal').val(total.toFixed(2));
                $('#modalChange').val((paid - total).toFixed(2));
            }

            // Submit Order
            $('#orderForm').submit(function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).text('Processing...');

                let data = {
                    customer_id: $('#customerSelect').val(),
                    subtotal: $('#modalSubtotal').val(),
                    discount: $('#modalDiscount').val(),
                    redeem_points: $('#redeemPoints').val(),
                    total_amount: $('#modalTotal').val(),
                    paid_amount: $('#modalPaid').val(),
                    payment_method: $('[name="payment_method"]').val(),
                    cart: cart
                };

                $.ajax({
                    url: "{{ route('orders.store') }}",
                    method: "POST",
                    data: data,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        if (res.success) {
                            alert("Success!");
                            cart = []; renderCart();
                            bootstrap.Modal.getInstance('#paymentModal').hide();
                            if (res.order_id) window.location.href = "/orders/" + res.order_id + "/print";
                            else location.reload();
                        }
                    },
                    error: function(err) {
                        btn.prop('disabled', false).text("{{ __('Complete Order') }}");
                        alert("Error: " + (err.responseJSON ? err.responseJSON.message : "Error"));
                    }
                });
            });
        });
    </script>
@endsection
