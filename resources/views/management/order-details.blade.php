@extends('management.layout.app') {{-- your layout file --}}
@section('title', 'Customer Order Details')

@section('content')

<div id="top" class="sa-app__body">
                <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
                    <div class="container container--max--xl">
                                                @include('management.layout.flash-messages')

                        <div class="py-5">
                            <div class="row g-4 align-items-center">
                                <div class="col">
                                    
                                    <h4 class="h4 m-0">View Complete Details of <span class="text-primary">Order #{{ $order->order_id }}</span></h4>
                                </div>
                                <div class="col-auto d-flex">
                                    <a href="{{ route('management.order.generate-invoice', $order->order_id) }}" class="btn btn-secondary me-3">Download Invoice</a>
                                    <!-- Update Status Button (Mockup - implementation requires modal logic similar to updateStatus) -->
                                    <button class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#updateOrderModal">Update Order Status</button>
                                    
                                    @if($order->order_payment_status != '1')
                                     <!-- Payment Received Order Form -->
                                    <form action="{{ route('management.order.update-payment-status', $order->order_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to update payment received for this order?');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="pay_status" value="1">
                                        <button type="submit" class="btn btn-success me-3">Update Payment Received</button>
                                    </form>
                                    
                                    @endif
                                    
                                    <!-- Cancel Order Form -->
                                    <form action="{{ route('management.order.update-status', $order->order_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="0">
                                        <button type="submit" class="btn btn-danger">Cancel Order</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="sa-page-meta mb-5">
                            <div class="sa-page-meta__body">
                                <div class="sa-page-meta__list">
                                    <div class="sa-page-meta__item">{{ date('d/m/Y h:i A', strtotime($order->order_date_time)) }}</div>
                                    <div class="sa-page-meta__item">{{ $order->order_items_qty }} items</div>
                                    <div class="sa-page-meta__item">Total ₹ {{ number_format($order->order_total_amt, 2) }}</div>
                                    
                                    @php
                                        // START: imohitmehto | 2026-08-25 | FIX: Status labels now match OrderStatus enum values
                                        // 0=Cancelled, 1=Pending, 2=Confirmed, 3=Delivered
                                        // Payment status
                                        $paymentStatusMap = [
                                            1 => ['Paid', 'success'],
                                            2 => ['Pending', 'warning'],
                                            0 => ['Failed', 'danger'], // optional fallback
                                        ];
                                    
                                        [$payLabel, $payBadge] = $paymentStatusMap[$order->order_payment_status]
                                            ?? ['Unknown', 'secondary'];
                                    
                                        // Order status
                                        $orderStatusMap = [
                                            0 => ['Cancelled',  'danger'],
                                            1 => ['Pending',    'warning'],
                                            2 => ['Confirmed',  'primary'],
                                            4 => ['Shipped / In Transit', 'info'],
                                            3 => ['Delivered',  'success'],
                                        ];
                                    
                                        [$orderLabel, $orderBadge] = $orderStatusMap[$order->order_status]
                                            ?? ['Unknown', 'secondary'];
                                        // END: imohitmehto | FIX: Status label mapping
                                    @endphp
                                    
                                    <div class="sa-page-meta__item d-flex align-items-center fs-6">
                                        <span class="badge badge-sa-{{ $payBadge }} me-2">
                                            {{ $payLabel }}
                                        </span>
                                    
                                        <span class="badge badge-sa-{{ $orderBadge }} me-2">
                                            {{ $orderLabel }}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="sa-entity-layout"
                            data-sa-container-query="{&quot;920&quot;:&quot;sa-entity-layout--size--md&quot;}">
                            <div class="sa-entity-layout__body">
                                <div class="sa-entity-layout__main">
                                    
                                    <div class="card mt-5">
                                        <div
                                            class="card-body px-5 py-4 d-flex align-items-center justify-content-between">
                                            <h2 class="mb-0 fs-exact-18 me-4">Order Items</h2>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="sa-table">
                                                <tbody>
                                                    @forelse($order->order_items as $item)
                                                        <tr>
                                                            <td class="min-w-20x" style="max-width: 200px;">
                                                                <div class="d-flex align-items-center">
                                                                    @if(isset($item['product_image']) && $item['product_image'])
                                                                        <img src="{{ $item['product_image'] }}" class="me-4" width="40" height="40" alt="">
                                                                    @else
                                                                         <div class="bg-secondary rounded-circle me-4 d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                                             <i class="fas fa-box"></i>
                                                                         </div>
                                                                    @endif

                                                    
                                                                    <div>
                                                                        {{ $item['product_name'] ?? 'Product Name' }}
                                                                        @if(!empty($item['variant_name']))
                                                                            <span class="fw-bold">({{ $item['variant_name'] }})</span>
                                                                        @endif
                                                                        @if(!empty($item['customization_option']) && $item['customization_option'] !== 'normal')
                                                                            <div class="text-muted mt-2">
                                                                                <span class="badge bg-info text-dark">Customization</span>
                                                                                <span class="ms-2">
                                                                                    {{ ucfirst($item['customization_option']) }} — ₹{{ number_format($item['customization_price'] ?? 0, 2) }}
                                                                                </span>
                                                                                @if(!empty($item['customization_text']))
                                                                                    <div class="small">Text: {{ $item['customization_text'] }}</div>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                    
                                                            <td class="text-end">₹ {{ number_format($item['product_rate'] ?? 0, 2) }}</td>
                                                            <td class="text-end">{{ $item['product_qty'] ?? 1 }}</td>
                                                            <td class="text-end">
                                                                ₹ {{ number_format(
                                                                    ($item['price'] ?? (($item['product_rate'] ?? 0) * ($item['product_qty'] ?? 1))),
                                                                    2
                                                                ) }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">No items found</td>
                                                        </tr>
                                                    @endforelse
                                                    </tbody>

                                                <tbody class="sa-table__group">
                                                    @php
                                                        $totalCharges = 0;
                                                        if(is_array($order->order_charges)) {
                                                            foreach($order->order_charges as $charge) {
                                                                $totalCharges += (float)($charge['calculated_amount'] ?? 0);
                                                            }
                                                        }
                                                        $subTotal = $order->order_total_amt - $totalCharges;
                                                    @endphp
                                                    <tr>
                                                        <td colSpan="3">Subtotal</td>
                                                        <td class="text-end">
                                                            <div class="sa-price"><span class="sa-price__symbol">₹ </span><span class="sa-price__integer">{{ number_format($subTotal, 2) }}</span></div>
                                                        </td>
                                                    </tr>
                                                    
                                                    @forelse($order->order_charges as $charge)
                                                        <tr>
                                                            <td colspan="3">
                                                                {{ $charge['charge_name'] ?? 'Charge' }}
                                                                &nbsp;
                                                                (
                                                                @if(($charge['charge_type'] ?? '') === 'fixed')
                                                                    ₹ {{ $charge['charge_value'] ?? 0 }}
                                                                @elseif(($charge['charge_type'] ?? '') === 'percentage')
                                                                    {{ $charge['charge_value'] ?? 0 }}%
                                                                @endif
                                                                )
                                                            </td>
                                                    
                                                            <td class="text-end">
                                                                ₹ {{ number_format((float)($charge['calculated_amount'] ?? 0), 2) }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">No charges</td>
                                                        </tr>
                                                    @endforelse
                                                    
                                                   
                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                        <td colSpan="3"><strong>Total Amount</strong></td>
                                                        <td class="text-end">
                                                            <div class="sa-price"><strong><span class="sa-price__symbol">₹ </span><span class="sa-price__integer">{{ number_format($order->order_total_amt, 2) }}</span></strong></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colSpan="3"><strong>Total Paid Amount</strong></td>
                                                        <td class="text-end">
                                                            <div class="sa-price"><strong><span class="sa-price__symbol">₹ </span><span class="sa-price__integer">{{ number_format($order->order_paid_amt, 2) }}</span></strong></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colSpan="3" class="text-danger"><strong>Total Due Amount</strong></td>
                                                        <td class="text-end text-danger">
                                                            <div class="sa-price"><strong><span class="sa-price__symbol">₹ </span><span class="sa-price__integer">{{ number_format($order->order_due_amt, 2) }}</span></strong></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Transactions Card (Placeholder or dynamic if transaction relation exists) -->
                                    <div class="card mt-5">
                                        <div class="card-body px-5 py-4 d-flex align-items-center justify-content-between">
                                            <h2 class="mb-0 fs-exact-18 me-4">Payment Transactions</h2>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="sa-table text-nowrap">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{ \App\Enums\OrderPaymentMode::label($order->order_payment_mode) }}
                                                            <div class="text-muted fs-exact-13">
                                                                TXN ID: {{ $order->order_payment_id ?? 'N/A' }}
                                                            </div>
                                                        </td>
                                                        <td>{{ $order->order_payment_date_time ? date('d/m/Y h:i A', strtotime($order->order_payment_date_time)) : '-' }}</td>
                                                        
                                                         @php
                                                            $paymentStatuses = [
                                                                1 => ['Paid', 'success'],
                                                                2 => ['Pending', 'warning'],
                                                                0 => ['Failed', 'danger'],
                                                            ];
                                                        
                                                            [$label, $class] = $paymentStatuses[$order->order_payment_status] ?? ['Unknown', 'secondary'];
                                                        @endphp
                                                        
                                                        <td>
                                                            <span class="badge badge-sa-{{ $class }} me-2">{{ $label }}</span>
                                                        </td>

                                                        <td class="text-end">
                                                            <div class="sa-price"><span class="sa-price__symbol">₹ </span><span class="sa-price__integer">{{ number_format($order->order_paid_amt, 2) }}</span></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="sa-entity-layout__sidebar">
                                    
                                    <div class="card">
                                        <div class="card-body d-flex align-items-center justify-content-between pb-0 pt-4">
                                            <h2 class="fs-exact-16 mb-0">Customer Details</h2>
                                        </div>
                                        <div class="card-body pt-4 fs-exact-14">
                                           <div class="d-flex mb-4"> 
                                                <div class="sa-symbol sa-symbol--shape--circle sa-symbol--size--lg">
                                                    @if($order->customer && $order->customer->cust_profile_photo)
                                                        <img src="{{ asset('uploads/customer-profile-photo/' . $order->customer->cust_profile_photo) }}" width="40" height="40" alt="" />
                                                    @else
                                                        <div class="sa-symbol__text">{{ substr($order->customer->cust_name ?? 'U', 0, 1) }}</div>
                                                    @endif
                                                </div>
                                                <div class="ms-3 ps-2">
                                                    <div class="fs-exact-14 fw-medium">{{ $order->customer->cust_name ?? 'Deleted User' }}</div>
                                                    <div class="fs-exact-13 text-muted">Customer ID: {{ $order->order_placed_cust_id }}</div>
                                                </div>
                                            </div>    
                                            
                                            <div class="mt-1">{{ $order->customer->cust_email ?? '-' }}</div>
                                            <div class="text-muted mt-1">{{ $order->customer->cust_mobile ?? '-' }}</div>
                                            
                                             <h2 class="fs-exact-16 mt-4 mb-0">Delivery Address</h2>
                                             <!-- Assuming order_delivery_details has 'address' key or similar -->
                                            @php
                                                $delivery = $order->order_delivery_details;
                                            @endphp
                                            
                                            <div class="mt-1 fw-semibold">
                                                {{ ($delivery['first_name'] ?? '') . ' ' . ($delivery['last_name'] ?? '') }}
                                            </div>
                                            
                                            <div class="text-muted fs-exact-13">
                                                {{ $delivery['phone'] ?? $order->customer->cust_mobile ?? 'N/A' }}<br>
                                                {{ $delivery['email'] ?? $order->customer->cust_email ?? 'N/A' }}
                                            </div>
                                            
                                            <div class="mt-1">
                                                {{ $delivery['address'] ?? 'N/A' }}
                                                @if(!empty($delivery['apartment']))
                                                    , {{ $delivery['apartment'] }}
                                                @endif
                                                <br>
                                                {{ $delivery['city'] ?? '' }}
                                                @if(!empty($delivery['state']))
                                                    , {{ $delivery['state'] }}
                                                @endif
                                                @if(!empty($delivery['pincode']))
                                                    - {{ $delivery['pincode'] }}
                                                @endif
                                                <br>
                                            </div>

                                            @if(!empty($order->order_gst_number))
                                            <div class="mt-3 pt-3 border-top">
                                                <h2 class="fs-exact-16 mb-2">GST Details</h2>
                                                <div class="text-muted fs-exact-13">
                                                    <strong>GST Number:</strong> {{ $order->order_gst_number }}<br>
                                                    @if($order->order_gst_amount > 0)
                                                    <strong>GST Amount:</strong> ₹{{ number_format($order->order_gst_amount, 2) }}
                                                    @endif
                                                </div>
                                            </div>
                                            @endif

                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Order Modal (Needs to be outside or ensuring IDs match trigger in real dynamic scenarios, but here it's single page so ID is static) -->
            <div class="modal fade" id="updateOrderModal" tabindex="-1" aria-labelledby="updateOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('management.order.update', $order->order_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateOrderModalLabel">Update Order Status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Order Status</label>
                                    <select class="form-select" name="order_status">
                                        <option value="1" {{ $order->order_status == '1' ? 'selected' : '' }}>Pending</option>
                                        <option value="2" {{ $order->order_status == '2' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="4" {{ $order->order_status == '4' ? 'selected' : '' }}>Shipped / In Transit</option>
                                        <option value="3" {{ $order->order_status == '3' ? 'selected' : '' }}>Delivered</option>
                                        <option value="0" {{ $order->order_status == '0' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
@endsection
            
