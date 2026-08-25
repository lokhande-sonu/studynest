<div class="modal fade" id="manageBundleProductsModal" tabindex="-1" aria-labelledby="manageBundleProductsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageBundleProductsModalLabel"><span id="manage_bundle_title">Manage Bundle Products</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="manageProductsForm" action="#" method="POST">
                @csrf
                <input type="hidden" id="manage_bundle_id" name="b_id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" id="productSearchInput" class="form-control" placeholder="Search products...">
                    </div>

                    <div class="product-list-container" style="max-height: 500px; overflow-y: auto;">
                        <div class="row" id="productList">
                            @foreach($products as $product)
                                <div class="col-md-3 col-sm-6 mb-3 product-item" data-name="{{ strtolower($product->p_name) }}">
                                    <div class="card h-100 border product-card-selectable" id="product_card_{{ $product->p_id }}">
                                        <div class="card-body p-2 text-center">
                                            <div class="form-check position-absolute top-0 start-0 m-2">
                                                <input class="form-check-input product-checkbox" type="checkbox" name="product_id[]" value="{{ $product->p_id }}" id="product_check_{{ $product->p_id }}">
                                            </div>
                                            <img src="{{ asset('uploads/product-photo/' . $product->p_photo) }}" class="img-fluid mb-2" style="height: 60px; object-fit: contain;" alt="">
                                            <h6 class="card-title small mb-1">{{ $product->p_name }}</h6>
                                            
                                            {{-- Quantity and Variant Selection (Hidden unless checked) --}}
                                            <div class="product-options mt-2 text-start" id="options_{{ $product->p_id }}" style="display: none;">
                                                <div class="mb-2">
                                                    <label class="x-small fw-bold">Qty:</label>
                                                    <input type="number" name="quantity[]" class="form-control form-control-sm" value="1" min="1" disabled id="qty_input_{{ $product->p_id }}">
                                                </div>
                                                @if($product->variants->count() > 0)
                                                <div>
                                                    <label class="x-small fw-bold">Variant:</label>
                                                    <select name="variant_id[]" class="form-select form-select-sm" disabled id="variant_select_{{ $product->p_id }}">
                                                        @foreach($product->variants as $variant)
                                                            <option value="{{ $variant->prod_variant_id }}">{{ $variant->prod_variant }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @else
                                                    <input type="hidden" name="variant_id[]" value="" disabled id="variant_select_{{ $product->p_id }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
