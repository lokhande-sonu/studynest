@extends('website.layouts.app')

@section('title', 'Study Nest - Cart')

@section('content')

    <!-- ================================ Cart Section Start ================================ -->
    <section class="cart py-20">
        <div class="container container-lg">
            @if(session('success'))
                <div class="alert alert-success mb-24">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mb-24">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-24">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row gy-4">
                <div class="col-xl-9 col-lg-8">
                    <div class="cart-items-container border border-gray-100 rounded-8 px-24 py-24 bg-white">
                        @if(count($cartItems) > 0)
                            @foreach($cartItems as $item)
                                <div class="cart-item border-bottom border-gray-100 py-24" data-id="{{ $item->id }}">
                                    <div class="row align-items-center gy-3">
                                        <div class="col-md-5">
                                            <div class="d-flex align-items-center gap-16">
                                                <a href="{{ route('website.product-details', $item->product_id) }}" class="flex-shrink-0">
                                                    <img src="{{ asset('uploads/product-photo/' . $item->product->p_photo) }}" alt="{{ $item->product->p_name }}" class="rounded-8 border border-gray-100" style="width: 80px; height: 80px; object-fit: cover;">
                                                </a>
                                                <div>
                                                    <h6 class="text-lg fw-semibold mb-8">
                                                        <a href="{{ route('website.product-details', $item->product_id) }}" class="text-gray-900 hover-text-main-600 line-clamp-2">{{ $item->product->p_name }}</a>
                                                    </h6>
                                                    @if($item->variant)
                                                        <span class="text-sm text-gray-500">Variant: {{ $item->variant->prod_variant }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-6">
                                             <span class="text-gray-500 text-sm d-block d-md-none">Price</span>
                                             <span class="text-lg fw-semibold text-gray-900">₹{{ number_format($item->price, 2) }}</span>
                                        </div>
                                        <div class="col-md-2 col-6">
                                            <span class="text-gray-500 text-sm d-block d-md-none">Quantity</span>
                                            @php $maxCap = isset($stockInfo[$item->id]) ? min((int)$stockInfo[$item->id], 5) : 5; @endphp
                                            <div class="d-flex align-items-center gap-8">
                                                <button type="button" class="btn btn-sm border border-gray-200 w-32 h-32 flex-center rounded-6 text-neutral-600 bg-gray-50 hover-bg-main-600 hover-text-white" onclick="changeCartQty({{ $item->id }}, -1, {{ $maxCap }})">
                                                    <i class="ph ph-minus"></i>
                                                </button>
                                                <input type="number" min="1" value="{{ $item->quantity }}" max="{{ $maxCap }}" class="form-control form-control-sm w-70 text-center" id="cart-qty-{{ $item->id }}" onchange="applyCartQty({{ $item->id }}, {{ $maxCap }})">
                                                <button type="button" class="btn btn-sm border border-gray-200 w-32 h-32 flex-center rounded-6 text-neutral-600 bg-gray-50 hover-bg-main-600 hover-text-white" onclick="changeCartQty({{ $item->id }}, 1, {{ $maxCap }})">
                                                    <i class="ph ph-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-6">
                                             <span class="text-gray-500 text-sm d-block d-md-none">Subtotal</span>
                                             <span class="text-lg fw-semibold text-main-600" id="row-subtotal-{{ $item->id }}">₹{{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </div>
                                        <div class="col-md-1 col-6 text-end">
                                            <button type="button" class="text-gray-400 hover-text-danger-600 transition-1" title="Remove Item" onclick="removeCartItem({{ $item->id }})">
                                                <i class="ph ph-trash text-2xl text-danger"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-40">
                                <div class="mb-24">
                                     <i class="ph ph-shopping-cart text-6xl text-gray-300"></i>
                                </div>
                                <h5 class="text-gray-900 mb-12">Your cart is empty</h5>
                                <p class="text-gray-500 mb-24">Looks like you haven't added anything to your cart yet.</p>
                                <a href="{{ route('website.shop') }}" class="btn btn-main rounded-pill px-32">Start Shopping</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <div class="cart-sidebar border border-gray-100 rounded-8 px-24 py-40">
                        
                        <!-- <div class="flex-between flex-wrap gap-16 mb-10">
                            <div class="flex-align gap-16 w-100">
                                <input type="text" class="common-input" placeholder="Coupon Code">
                                <button type="submit" class="btn btn-main py-18 rounded-8 flex-shrink-0">Apply Coupon</button>
                            </div>
                        </div> -->

                        <div class="bg-color-three rounded-8 p-24" id="charges-box">
                            <div class="mb-10 flex-between gap-8">
                                <span class="text-gray-900 font-heading-two">Subtotal</span>
                                <span class="text-gray-900 fw-semibold" id="cart-subtotal">₹{{ $subTotal }}</span>
                            </div>
                            
                            @if($subTotal > 0)
                                @foreach($calculatedCharges as $charge)
                                <div class="mb-32 flex-between gap-8 charge-row" data-charge-type="{{ $charge->charge_type }}" data-charge-value="{{ $charge->charge_value }}">
                                    <div class="">
                                        <span class="text-gray-900 font-heading-two d-block">{{ $charge->charge_name }}</span>
                                        @if($charge->charge_type === 'percentage')
                                            <span class="text-sm text-gray-400">({{ $charge->charge_value }}%)</span>
                                        @endif
                                    </div>
                                    <span class="text-gray-900 fw-semibold charge-amount">₹{{ $charge->calculated_amount }}</span>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="bg-color-three rounded-8 p-24 mt-24">
                            <div class="flex-between gap-8">
                                <span class="text-gray-900 text-xl fw-semibold">Total</span>
                                <span class="text-gray-900 text-xl fw-semibold" id="cart-total">₹{{ $grandTotal }}</span>
                            </div>
                        </div>

                        <div class="mt-10">
                            <div class="payment-item">
                                <div class="form-check common-check common-radio py-16 mb-0">
                                    <input class="form-check-input" type="radio" name="payment" id="payment2" value="online" checked>
                                    <label class="form-check-label fw-semibold text-neutral-600" for="payment2">Online Payment</label>
                                </div>
                                <div class="payment-item__content px-16 py-24 rounded-8 bg-main-50 position-relative">
                                    <p class="text-gray-800">Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.</p>
                                </div>
                            </div>
                            <div class="payment-item">
                                <div class="form-check common-check common-radio py-16 mb-0">
                                    <input class="form-check-input" type="radio" name="payment" id="payment3" value="cod">
                                    <label class="form-check-label fw-semibold text-neutral-600" for="payment3">Cash on Delivery</label>
                                </div>
                                <div class="payment-item__content px-16 py-24 rounded-8 bg-main-50 position-relative">
                                    <p class="text-gray-800">Make your payment at the time of delivery. Please use your Order ID for payment reference.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 pt-10 border-top border-gray-100">
                            <p class="text-gray-500">Your data will be used to process your order, support your experience throughout this website, and for other purposes described in our <a href="#" class="text-main-600 text-decoration-underline"> privacy policy</a> .</p>
                        </div>

                        @auth
                            <button type="button" id="proceed-to-checkout-btn" class="btn btn-main mt-40 py-18 w-100 rounded-8 {{ $subTotal == 0 ? 'disabled' : '' }}" disabled data-subtotal="{{ $subTotal }}" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                                Proceed to Checkout
                            </button>
                        @else
                            <button type="button" class="btn btn-main mt-40 py-18 w-100 rounded-8 {{ $subTotal == 0 ? 'disabled' : '' }}" {{ $subTotal == 0 ? 'disabled' : '' }} data-bs-toggle="modal" data-bs-target="#loginModal">
                                Login to Checkout
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-16 border-0">
                <div class="modal-header border-0 pb-0 px-24 pt-24">
                    <div>
                        <h5 class="modal-title font-heading-two text-lg" id="checkoutModalLabel">Confirm Delivery Address</h5>
                        <p class="text-gray-500 text-sm mt-4">Items will be delivered to this address</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <form action="{{ route('website.checkout.place-order') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" id="modal_payment_method">
                        <div class="row gy-3">
                            @php
                                $names = explode(' ', auth()->user()->cust_name ?? '');
                                $firstName = $names[0] ?? '';
                                $lastName = isset($names[1]) ? implode(' ', array_slice($names, 1)) : '';
                            @endphp
                            <div class="col-sm-6">
                                <label class="form-label text-heading-two text-sm fw-semibold mb-8">First Name <span class="text-danger-600">*</span></label>
                                <input type="text" class="common-input border-gray-200 py-8" placeholder="First Name" name="first_name" value="{{ $firstName }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-heading-two text-sm fw-semibold mb-8">Last Name <span class="text-danger-600">*</span></label>
                                <input type="text" class="common-input border-gray-200 py-8" placeholder="Last Name" name="last_name" value="{{ $lastName }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-heading-two text-sm fw-semibold mb-8">Email <span class="text-danger-600">*</span></label>
                                <input type="email" class="common-input border-gray-200 py-8" placeholder="Email Address" name="email" value="{{ auth()->user()->cust_email ?? '' }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-heading-two text-sm fw-semibold mb-8">Phone <span class="text-danger-600">*</span></label>
                                <input type="number" class="common-input border-gray-200 py-8" placeholder="Phone Number" name="phone" value="{{ auth()->user()->cust_mobile ?? '' }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-heading-two text-sm fw-semibold mb-8">Address <span class="text-danger-600">*</span></label>
                                <input type="text" class="common-input border-gray-200 py-8" placeholder="House number and street name" name="address" value="{{ auth()->user()->cust_address ?? '' }}" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label text-heading-two text-sm fw-semibold mb-8">City <span class="text-danger-600">*</span></label>
                                <input type="text" class="common-input border-gray-200 py-8" placeholder="City" name="city" value="{{ auth()->user()->cust_city ?? '' }}" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label text-heading-two text-sm fw-semibold mb-8">State <span class="text-danger-600">*</span></label>
                                <input type="text" class="common-input border-gray-200 py-8" placeholder="State" name="state" value="{{ auth()->user()->cust_state ?? '' }}" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label text-heading-two text-sm fw-semibold mb-8">Pincode <span class="text-danger-600">*</span></label>
                                <input type="text" class="common-input border-gray-200 py-8" placeholder="Pincode" name="pincode" value="{{ auth()->user()->cust_pincode ?? '' }}" required>
                            </div>
                            <div class="col-12 mt-16">
                                <button type="submit" class="btn btn-main w-100 rounded-8 py-14 fw-normal">Confirm & Pay</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentRadios = document.querySelectorAll('input[name="payment"]');
            const proceedBtn = document.getElementById('proceed-to-checkout-btn');
            const modalPaymentInput = document.getElementById('modal_payment_method');

            if (proceedBtn) {
                const subTotal = parseFloat(proceedBtn.getAttribute('data-subtotal')) || 0;

                function updateButtonState() {
                    const checkedRadio = document.querySelector('input[name="payment"]:checked');
                    if (checkedRadio && subTotal > 0) {
                        proceedBtn.removeAttribute('disabled');
                        proceedBtn.classList.remove('disabled');
                        modalPaymentInput.value = checkedRadio.value;
                    } else {
                        proceedBtn.setAttribute('disabled', 'disabled');
                        proceedBtn.classList.add('disabled');
                        modalPaymentInput.value = '';
                    }
                }

                paymentRadios.forEach(radio => {
                    radio.addEventListener('change', updateButtonState);
                });

                // Initial check
                updateButtonState();
            }
        });

        function formatINR(n) {
            return '₹' + Number(n).toFixed(2);
        }
        function recomputeChargesAndTotal(subtotal) {
            const subtotalEl = document.getElementById('cart-subtotal');
            if (subtotalEl) subtotalEl.textContent = formatINR(subtotal);
            let total = subtotal;
            document.querySelectorAll('.charge-row').forEach(function(row){
                const type = row.getAttribute('data-charge-type');
                const val = parseFloat(row.getAttribute('data-charge-value') || '0');
                let amt = 0;
                if (type === 'flat') {
                    amt = val;
                } else if (type === 'percentage') {
                    amt = subtotal * (val / 100);
                }
                const amtEl = row.querySelector('.charge-amount');
                if (amtEl) amtEl.textContent = formatINR(amt);
                total += amt;
            });
            const totalEl = document.getElementById('cart-total');
            if (totalEl) totalEl.textContent = formatINR(total);
            const proceedBtn = document.getElementById('proceed-to-checkout-btn');
            if (proceedBtn) {
                if (subtotal > 0) {
                    proceedBtn.removeAttribute('disabled');
                    proceedBtn.classList.remove('disabled');
                } else {
                    proceedBtn.setAttribute('disabled', 'disabled');
                    proceedBtn.classList.add('disabled');
                }
            }
        }
        async function updateCartItemQty(id, qty) {
            const fd = new FormData();
            fd.append('id', id);
            fd.append('quantity', String(qty));
            fd.append('_method', 'PATCH');
            const resp = await fetch("{{ route('website.cart.update') }}", {
                method: 'POST',
                headers: {'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: fd
            });
            const data = await resp.json();
            if (data && data.success) {
                const row = document.getElementById('row-subtotal-' + id);
                if (row) row.textContent = formatINR(data.row_subtotal);
                recomputeChargesAndTotal(parseFloat(data.total || 0));
            } else {
                window.snToast('error', data.message || 'Unable to update quantity');
                return false;
            }
            return true;
        }
        function changeCartQty(id, delta, maxCap) {
            const input = document.getElementById('cart-qty-' + id);
            if(!input) return;
            let v = parseInt(input.value || '1', 10);
            if (isNaN(v)) v = 1;
            v += delta;
            if (v < 1) v = 1;
            const max = parseInt(input.max || String(maxCap || 5), 10);
            if (max > 0 && v > max) v = max;
            input.value = v;
            updateCartItemQty(id, v);
        }
        function applyCartQty(id, maxCap) {
            const input = document.getElementById('cart-qty-' + id);
            if(!input) return;
            let v = parseInt(input.value || '1', 10);
            if (isNaN(v) || v < 1) v = 1;
            const max = parseInt(input.max || String(maxCap || 5), 10);
            if (max > 0 && v > max) v = max;
            input.value = v;
            updateCartItemQty(id, v);
        }
        async function removeCartItem(id) {
            const fd = new FormData();
            fd.append('id', id);
            fd.append('_method', 'DELETE');
            const resp = await fetch("{{ route('website.cart.remove') }}", {
                method: 'POST',
                headers: {'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: fd
            });
            if (resp.status === 302) { // fallback if server redirects
                window.location.reload();
                return;
            }
            const data = await resp.json();
            if (data && data.success) {
                const itemEl = document.querySelector('.cart-item[data-id="'+id+'"]');
                if (itemEl) itemEl.remove();
                recomputeChargesAndTotal(parseFloat(data.total || 0));
                const badge = document.getElementById('header-cart-count');
                if (badge && typeof data.cart_count !== 'undefined') {
                    badge.textContent = String(data.cart_count);
                }
                window.snToast('success', 'Item removed from cart');
                // If no items left, reload for empty state rendering quickly
                if (document.querySelectorAll('.cart-item').length === 0) {
                    window.location.reload();
                }
            } else {
                window.snToast('error', data.message || 'Unable to remove item');
            }
        }
    </script>
@endsection

@section('scripts')
<script type="text/javascript">
    function showAlert(message, type = 'success') {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                        message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';
        $('#alert-container').html(alertHtml);
        // Auto dismiss after 3 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    }

    $(".remove-from-cart").click(function (e) {
        e.preventDefault();
        var ele = $(this);
        if(confirm("Are you sure want to remove?")) {
            $.ajax({
                url: '{{ route('website.cart.remove') }}',
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE',
                    id: ele.closest(".cart-item").attr("data-id")
                },
                success: function (response) {
                    if(response.success) {
                        // Reload page to recalculate all charges and totals correctly
                        location.reload(); 
                    } else {
                        showAlert(response.message || "Failed to remove item. Please try again.", 'danger');
                    }
                },
                error: function() {
                    showAlert('Error removing item', 'danger');
                }
            });
        }
    });
</script>
@endsection
