@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="bi bi-box-seam-fill"></i> {{ __('Products') }}</h3>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" id="createNewProduct">
            <i class="bi bi-plus-lg"></i> {{ __('Add New Product') }}
        </button>
    </div>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-gradient bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-list-stars"></i> {{ __('Product List') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="py-3 ps-4" width="5%">{{ __('ID') }}</th>
                            <th class="py-3" width="10%">{{ __('Image') }}</th>
                            <th class="py-3" width="20%">{{ __('Name') }}</th>
                            <th class="py-3">{{ __('Variants & Price') }}</th>
                            <th class="py-3 text-center" width="15%">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#{{ $loop->iteration }}</td>
                                <td>
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="img-thumbnail shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                                            <i class="bi bi-image-fill fs-4 opacity-25"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-5">{{ $product->name }}</span>
                                    <br>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-tag-fill"></i> {{ $product->brand ?? __('Unknown') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($product->variants->count() > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($product->variants as $variant)
                                                <div class="badge bg-white text-dark border shadow-sm p-2 rounded-3 text-start">
                                                    <span class="fw-bold text-primary">{{ $variant->variant_name }}</span>
                                                    <br>
                                                    <span class="small text-muted text-uppercase">{{ $variant->sku }}</span>
                                                    <div class="fw-bold text-success mt-1">{{ number_format($variant->price, 2) }} Tk</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger">
                                            {{ __('No Variant Added') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm rounded-pill" role="group">
                                        <button class="btn btn-sm btn-outline-primary edit-btn" data-id="{{ $product->id }}" title="{{ __('Edit') }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $product->id }}" title="{{ __('Delete') }}">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    {{ __('No products found. Please add a new product.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="bi bi-plus-circle-fill"></i> {{ __('Add New Product') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" id="productId" name="product_id">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">{{ __('Product Name') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-box-seam"></i></span>
                                <input type="text" name="name" id="productName" class="form-control border-start-0" placeholder="{{ __('Ex: T-Shirt') }}" required>
                                <button type="button" class="btn btn-outline-secondary border-start-0 voice-btn" title="{{ __('Speak') }}">
                                    <i class="bi bi-mic-fill text-primary"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('Brand') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-tag"></i></span>
                                <input type="text" name="brand" id="productBrand" class="form-control border-start-0" placeholder="{{ __('Ex: Nike') }}">
                                <button type="button" class="btn btn-outline-secondary border-start-0 voice-btn" title="{{ __('Speak') }}">
                                    <i class="bi bi-mic-fill text-primary"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('Product Image') }}</label>
                        <div class="d-flex align-items-center gap-3">
                            <div id="imagePreviewContainer" class="d-none">
                                <img id="imagePreview" src="" alt="Preview" class="img-thumbnail rounded-3" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="image" id="productImage" class="form-control" accept="image/*">
                                <small class="text-muted">Supports JPG, PNG, GIF (Max 2MB). Mobile users can use camera.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('Description') }}</label>
                        <div class="input-group">
                            <textarea name="description" id="productDescription" class="form-control" rows="2" placeholder="{{ __('Enter product details...') }}"></textarea>
                            <button type="button" class="btn btn-outline-secondary voice-btn" title="{{ __('Speak') }}">
                                <i class="bi bi-mic-fill text-primary"></i>
                            </button>
                        </div>
                    </div>

                    <div id="variantsSection">
                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-layers-fill"></i> {{ __('Product Variants') }}</h6>
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" id="addVariantRow">
                                <i class="bi bi-plus-lg"></i> {{ __('Add Variant') }}
                            </button>
                        </div>

                        <div class="table-responsive bg-light rounded-3 p-2 border">
                            <table class="table table-borderless mb-0" id="variantTable">
                                <thead class="text-secondary small text-uppercase">
                                    <tr>
                                        <th width="35%">{{ __('Variant Name') }}</th>
                                        <th width="30%">{{ __('SKU / Code') }}</th>
                                        <th width="25%">{{ __('Price') }}</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    </tbody>
                            </table>
                        </div>
                        <small class="text-danger" id="variantEditNotice" style="display: none;">
                            * Variant editing is not supported in this quick edit mode.
                        </small>
                    </div>

                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="saveBtn">
                        <i class="bi bi-check-lg"></i> <span id="saveBtnText">{{ __('Save Product') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="module">
    $(document).ready(function() {
        let productModal = new bootstrap.Modal(document.getElementById('productModal'));

        // --- 1. Voice Input Function ---
        $(document).on('click', '.voice-btn', function() {
            if (!('webkitSpeechRecognition' in window)) { alert("Try Google Chrome for Voice Input."); return; }
            let btn = $(this), icon = btn.find('i'), inputField = btn.closest('.input-group').find('input, textarea');
            const recognition = new webkitSpeechRecognition();
            recognition.lang = '{{ app()->getLocale() == "bn" ? "bn-BD" : "en-US" }}';
            recognition.continuous = false; recognition.interimResults = false;

            recognition.onstart = function() {
                icon.removeClass('bi-mic-fill text-primary').addClass('bi-mic-mute-fill text-danger spinner-grow spinner-grow-sm');
                inputField.attr('placeholder', 'Listening...');
            };

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                if(inputField.attr('type') === 'number') inputField.val(transcript.replace(/[^0-9.]/g, ''));
                else inputField.val(transcript);
            };

            recognition.onend = function() {
                icon.removeClass('bi-mic-mute-fill text-danger spinner-grow spinner-grow-sm').addClass('bi-mic-fill text-primary');
                inputField.attr('placeholder', '');
            };
            recognition.start();
        });

        // --- 2. Image Preview Function ---
        $('#productImage').change(function(){
            const file = this.files[0];
            if (file){
                let reader = new FileReader();
                reader.onload = function(event){
                    $('#imagePreview').attr('src', event.target.result);
                    $('#imagePreviewContainer').removeClass('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                $('#imagePreviewContainer').addClass('d-none');
            }
        });

        // --- 3. Reset Modal for Add New ---
        function resetModalForAdd() {
            $('#productForm')[0].reset();
            $('#productId').val('');
            $('#modalTitle').html('<i class="bi bi-plus-circle-fill"></i> {{ __("Add New Product") }}');
            $('#saveBtnText').text('{{ __("Save Product") }}');

            $('#imagePreviewContainer').addClass('d-none');
            $('#variantTable tbody').empty();
            addVariantRow(0); // Add one initial row

            $('#variantsSection').show();
            $('#addVariantRow').show();
            $('#variantEditNotice').hide();
        }

        $('#createNewProduct').click(function() {
            resetModalForAdd();
            productModal.show();
        });

        // --- 4. Edit Product Click Handler ---
        $(document).on('click', '.edit-btn', function() {
            let productId = $(this).data('id');
            $('#productForm')[0].reset();
            $('#variantTable tbody').empty();

            $.ajax({
                url: "/products/" + productId + "/edit",
                method: "GET",
                success: function(data) {
                    $('#productId').val(data.id);
                    $('#productName').val(data.name);
                    $('#productBrand').val(data.brand);
                    $('#productDescription').val(data.description);

                    if(data.image_url) {
                        $('#imagePreview').attr('src', data.image_url);
                        $('#imagePreviewContainer').removeClass('d-none');
                    } else {
                        $('#imagePreviewContainer').addClass('d-none');
                    }

                    $('#modalTitle').html('<i class="bi bi-pencil-square"></i> {{ __("Edit Product") }}');
                    $('#saveBtnText').text('{{ __("Update Product") }}');

                    $('#variantsSection').show();
                    $('#addVariantRow').hide();
                    $('#variantEditNotice').show();

                    if(data.variants.length > 0) {
                         data.variants.forEach(variant => {
                             $('#variantTable tbody').append(`
                                 <tr>
                                     <td><input type="text" class="form-control form-control-sm" value="${variant.variant_name}" disabled></td>
                                     <td><input type="text" class="form-control form-control-sm" value="${variant.sku}" disabled></td>
                                     <td><input type="text" class="form-control form-control-sm" value="${variant.price}" disabled></td>
                                     <td></td>
                                 </tr>
                             `);
                         });
                    }
                    productModal.show();
                },
                error: function() {
                    alert('Could not fetch product data.');
                }
            });
        });

        // --- 5. Submit Form (AJAX with Fix) ---
        $('#productForm').submit(function(e) {
            e.preventDefault();
            let submitBtn = $('#saveBtn');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

            let formData = new FormData(this);
            let productId = $('#productId').val();
            let url = "{{ route('products.store') }}";

            // Logic to handle Update (Method Spoofing for FormData)
            if(productId) {
                url = "/products/" + productId;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                method: 'POST', // Always POST for FormData, _method handles PUT
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.success) {
                        productModal.hide();
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> <span id="saveBtnText">{{ __("Save Product") }}</span>');
                    let errorMsg = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = '';
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            errorMsg += value[0] + '\n';
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                }
            });
        });

        // --- 6. Variant Rows Logic ---
        let rowCount = 1;
        $('#addVariantRow').click(function() { addVariantRow(rowCount); rowCount++; });

        function addVariantRow(count) {
            let html = `
            <tr>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" name="variants[${count}][name]" class="form-control" required placeholder="{{ __('Red-L') }}">
                        <button type="button" class="btn btn-outline-secondary voice-btn"><i class="bi bi-mic-fill"></i></button>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" name="variants[${count}][sku]" class="form-control" required placeholder="{{ __('SKU-123') }}">
                        <button type="button" class="btn btn-outline-secondary voice-btn"><i class="bi bi-mic-fill"></i></button>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="variants[${count}][price]" class="form-control" required placeholder="0.00">
                        <button type="button" class="btn btn-outline-secondary voice-btn"><i class="bi bi-mic-fill"></i></button>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-circle"><i class="bi bi-x-lg"></i></button>
                </td>
            </tr>`;
            $('#variantTable tbody').append(html);
        }

        $(document).on('click', '.remove-row', function() {
             if ($('#variantTable tbody tr').length > 1) { $(this).closest('tr').remove(); }
             else { alert("At least one variant is required!"); }
        });

        $(document).on('click', '.delete-btn', function() {
            if (confirm("Are you sure?")) {
                $.ajax({
                    url: "/products/" + $(this).data('id'), method: "DELETE",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) { if (res.success) { alert(res.message); location.reload(); } },
                    error: function() { alert('Error processing request.'); }
                });
            }
        });

    });
</script>
@endsection
