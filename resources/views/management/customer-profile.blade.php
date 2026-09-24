@extends('management.layout.app') {{-- your layout file --}}
@section('title', 'Customer Profile')

@section('content')

<div id="top" class="sa-app__body">
                <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
                    <div class="container">
                                    @include('management.layout.flash-messages')

                        <div class="py-5">
                            <div class="row g-4 align-items-center">
                                <div class="col">

                                    <h4 class="h4 m-0">Customer Detailed Profile</h4>
                                    <p class="mt-2">Manage particular customer detailed profile</p>
                                </div>
                                
                            </div>
                        </div>
                        <div class="sa-entity-layout"
                            data-sa-container-query="{&quot;920&quot;:&quot;sa-entity-layout--size--md&quot;}">
                            <div class="sa-entity-layout__body">
                                <div class="sa-entity-layout__sidebar">
                                    <div class="card">
                                        <div class="card-body d-flex flex-column align-items-center">
                                            <div class="pt-3">
                                                <div class="sa-symbol sa-symbol--shape--circle"
                                                    style="--sa-symbol--size:6rem">
                                                    @if($customer->cust_profile_photo)
                                                        <img src="{{ asset(env('CUSTOMER_PROFILE_PHOTO') . '/' . $customer->cust_profile_photo) }}" width="96" height="96" alt="" />
                                                    @else
                                                        <!-- Placeholder or Initials if no photo -->
                                                        <div class="sa-symbol__text display-4">{{ substr($customer->cust_name, 0, 1) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-center mt-4">
                                                <div class="fs-exact-16 fw-medium">{{ $customer->cust_name }}</div>
                                                <div class="fs-exact-13 text-muted">
                                                    <div class="mt-1"><a href="mailto:{{ $customer->cust_email }}">{{ $customer->cust_email }}</a></div>
                                                    <div class="mt-1">{{ $customer->cust_mobile }}</div>
                                                </div>
                                            </div>
                                            <div class="sa-divider my-5"></div>
                                            <div class="w-100">
                                                <dl class="list-unstyled m-0">
                                                    <dt class="fs-exact-14 fw-medium">Gender</dt>
                                                    <dd class="fs-exact-13 text-muted mb-0 mt-1">{{ ucfirst($customer->cust_gender ?? 'N/A') }}</dd>
                                                </dl>
                                                <dl class="list-unstyled m-0 mt-4">
                                                    <dt class="fs-exact-14 fw-medium">Registration Date</dt>
                                                    <dd class="fs-exact-13 text-muted mb-0 mt-1">{{ optional($customer->cust_created_at)->format('d/m/Y h:i A') }}</dd>
                                                </dl>
                                                <dl class="list-unstyled m-0 mt-4">
                                                    <dt class="fs-exact-14 fw-medium">City</dt>
                                                    <dd class="fs-exact-13 text-muted mb-0 mt-1">{{ $customer->cust_city ?? 'N/A' }}</dd>
                                                </dl>
                                                <dl class="list-unstyled m-0 mt-4">
                                                    <dt class="fs-exact-14 fw-medium">Address</dt>
                                                    <dd class="fs-exact-13 text-muted mb-0 mt-1">{{ $customer->cust_address ?? 'N/A' }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sa-entity-layout__main">
                                    <div class="card mt-5">
                                        <div
                                            class="card-body px-5 py-4 d-flex align-items-center justify-content-between">
                                            <h2 class="mb-0 fs-exact-18 me-4">Update Customer Details</h2>
                                            <hr/>
                                        </div>
                                        <form action="{{ route('management.customers.update', $customer->cust_id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="row px-5">
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label class="form-label">Customer Name</label>
                                                        <input type="text" name="cust_name" class="form-control" value="{{ $customer->cust_name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label class="form-label">Customer Email</label>
                                                        <input type="email" name="cust_email" class="form-control" value="{{ $customer->cust_email }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label class="form-label">Customer Phone Number</label>
                                                        <input type="number" name="cust_mobile" class="form-control" value="{{ $customer->cust_mobile }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label class="form-label">Customer Gender</label>
                                                        <select name="cust_gender" class="form-select">
                                                            <option value="Male" {{ ($customer->cust_gender == 'Male') ? 'selected' : '' }}>Male</option>
                                                            <option value="Female" {{ ($customer->cust_gender == 'Female') ? 'selected' : '' }}>Female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label class="form-label">Customer Photo</label>
                                                        <input type="file" name="cust_profile_photo" class="form-control" >
                                                    </div>
                                                </div>
                                                
                                                 <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label class="form-label">State</label>
                                                        <select name="cust_state" class="form-select">
                                                            
                                                            <option value="Madhya Pradesh" {{ ($customer->cust_state == 'Madhya Pradesh') ? 'selected' : '' }}>Madhya Pradesh</option>
                                                            <option value="Other" {{ ($customer->cust_state == 'Other') ? 'selected' : '' }}>Other</option>
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="col-md-4">
                                                    <div class="mb-4">
                                                        <label class="form-label">Customer City</label>
                                                        <input type="text" name="cust_city" class="form-control" value="{{ $customer->cust_city }}" required>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="mb-4">
                                                        <label class="form-label">Customer Pincode</label>
                                                        <input type="number" name="cust_pincode" class="form-control" value="{{ $customer->cust_pincode }}" required>
                                                    </div>
                                                </div>
                                               
                                                <div class="col-md-4">
                                                    <div class="mb-4">
                                                        <label class="form-label">Account Status</label>
                                                        <select name="cust_status" class="form-select">
                                                            
                                                            <option value="1" {{ ($customer->cust_status == '1') ? 'selected' : '' }}>Active</option>
                                                            <option value="0" {{ ($customer->cust_status == '0') ? 'selected' : '' }}>Disabled</option>
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                 <div class="col-md-12">
                                                    <div class="mb-4">
                                                        <label class="form-label">Customer Full Address</label>
                                                        <input type="text" class="form-control" value="{{ $customer->cust_address }}" name="cust_address" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-4">
                                                        <button type="submit" class="btn btn-primary">Update Details</button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </form> 
                                    </div>
                                    
                                    <div class="card mt-5">
                                        <div
                                            class="card-body px-5 py-4 d-flex align-items-center justify-content-between">
                                            <h2 class="mb-0 fs-exact-18 me-4">Recent Orders</h2>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="sa-table text-nowrap">
                                                <tbody>
                                                    @foreach($customer->orders as $order)
                                                    <tr>
                                                        <td><a href="{{ route('management.order.details', $order->order_id) }}">#{{ $order->order_id }}</a></td>
                                                        <td>{{ date('d/m/Y h:i A', strtotime($order->order_date_time)) }}</td>
                                                        @php
                                                            $statusMap = [
                                                                0 => ['label' => 'Cancelled',  'badge' => 'danger'],
                                                                1 => ['label' => 'Pending',    'badge' => 'warning'],
                                                                2 => ['label' => 'Confirmed',  'badge' => 'primary'],
                                                                4 => ['label' => 'Shipped / In Transit', 'badge' => 'info'],
                                                                3 => ['label' => 'Delivered',  'badge' => 'success'],
                                                            ];
                                                        
                                                            $status = $statusMap[$order->order_status] ?? [
                                                                'label' => 'Unknown',
                                                                'badge' => 'secondary'
                                                            ];
                                                        @endphp
                                                        
                                                        <td>
                                                            <div class="badge badge-sa-{{ $status['badge'] }}">
                                                                {{ $status['label'] }}
                                                            </div>
                                                        </td>

                                                        <td>{{ $order->total_items }} items</td>
                                                        <td>₹ {{ number_format($order->order_total_amt, 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                    @if($customer->orders->isEmpty())
                                                        <tr><td colspan="5" class="text-center text-muted">No recent orders found.</td></tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="sa-divider"></div>
                                        <div class="px-5 py-4 text-center"><a href="{{ route('management.order.index') }}">View all
                                                orders</a></div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                        @endsection