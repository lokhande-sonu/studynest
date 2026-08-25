@extends('management.layout.app') {{-- your layout file --}}
@section('title', 'Customer Report')

@section('content')
<div id="top" class="sa-app__body">
                <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
                    <div class="container">
                        @include('management.layout.flash-messages')
                        <div class="py-5">
                            <div class="row g-4 align-items-center">
                                <div class="col">

                                    <h4 class="h4 m-0">Customers Report</h4>
                                    <p class="mt-2">Filter and Export Customer Data</p>
                                </div>
                                
                            </div>
                        </div>
                        <div class="card">
                            <div class="sa-chart-toolbar p-4">
                                    <div class="mb-4">
                                            <input type="text" placeholder="Start Customers by Customer Name, Mobile Number & Email Address"
                                            class="form-control form-control--search" id="table-search" />
                                        </div>
                                    <form method="GET" action="{{ route('management.customer-report.index') }}" class="sa-chart-toolbar__body">

                                        <div class="sa-chart-toolbar__item me-auto">
                                            <label class="sa-chart-toolbar__item-label">Registration Date</label>
                                            <div class="d-flex">
                                                <input type="date" name="from_date" class="form-control form-control-sm"
                                                       value="{{ request('from_date') }}">
                                                <span class="mx-2">-</span>
                                                <input type="date" name="to_date" class="form-control form-control-sm"
                                                       value="{{ request('to_date') }}">
                                            </div>
                                        </div>
                                    
                                        <div class="sa-chart-toolbar__item">
                                            <label class="sa-chart-toolbar__item-label">Account Status</label>
                                            <select name="cust_status" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                <option value="1" {{ request('cust_status')=='1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ request('cust_status')=='0' ? 'selected' : '' }}>Blocked</option>
                                            </select>
                                        </div>
                                    
                                        <div class="sa-chart-toolbar__item">
                                            <button class="btn btn-primary btn-sm me-2">Filter</button>
                                    
                                            <a href="{{ route('management.customer-report.export', request()->query()) }}"
                                               class="btn btn-success btn-sm me-2">Export Excel</a>
                                    
                                            <a href="{{ route('management.customer-report.export-pdf', request()->query()) }}"
                                               class="btn btn-danger btn-sm">Export PDF</a>
                                        </div>
                                    
                                    </form>
                                </div>
                                    <div class="sa-divider"></div>
                            <table class="sa-datatables-init" data-order="[[ 1, &quot;asc&quot; ]]"
                                data-sa-search-input="#table-search">
                                <thead>
                                    <tr>
                                        <th class="w-min">ID</th>
                                        <th>Customer Details</th>
                                        <th>Registered</th>
                                        <th>Gender</th>
                                        <th>City</th>
                                        <th>Account Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td>{{ $customer->cust_id }}</td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg me-4">
                                                    @if($customer->cust_profile_photo)
                                                         <img src="{{ asset(env('CUSTOMER_PROFILE_PHOTO') . '/' . $customer->cust_profile_photo) }}" width="40" height="40" alt="">
                                                    @else
                                                        <div class="sa-symbol__text">{{ substr($customer->cust_name, 0, 1) }}</div>
                                                    @endif
                                                </div>
                                                <div>{{ $customer->cust_name }}
                                                    <div class="text-muted mt-n1">{{ $customer->cust_mobile }} | {{ $customer->cust_email }}</div>
                                                </div>
                                            </div>
                                          
                                        </td>
                                        <td>{{ optional($customer->cust_created_at)->format('d/m/Y h:i A') }}</td>
                                        <td>{{ ucfirst($customer->cust_gender ?? 'N/A') }}</td>
                                        <td>{{ $customer->cust_city ?? 'N/A' }}</td>
                                        <td>
                                            @if($customer->cust_status == 1)
                                                <div class="badge badge-sa-success">Active</div>
                                            @else
                                                <div class="badge badge-sa-danger">Blocked</div>
                                            @endif
                                        </td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    @endsection        