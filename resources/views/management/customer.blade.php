@extends('management.layout.app') {{-- your layout file --}}
@section('title', 'Customer Management')

@section('content')

<div id="top" class="sa-app__body">
                <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
                    <div class="container">
                        @include('management.layout.flash-messages')

                        <div class="py-5">
                            <div class="row g-4 align-items-center">
                                <div class="col">

                                    <h4 class="h4 m-0">Customer Management</h4>
                                    <p class="mt-2">Manage list of all the registered customers</p>
                                </div>
                                <div class="col-auto d-flex">
                                    <a href="{{ route('management.customer-report.export') }}" class="btn btn-primary">Export Customers</a>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="p-4"><input type="text" placeholder="Search Customers by Name, Mobile or Email"
                                    class="form-control form-control--search mx-auto" id="table-search" /></div>
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
                                        <th class="w-min" data-orderable="false">Action</th>
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
                                        <td>
                                            <div class="dropdown"><button class="btn btn-sa-muted btn-sm" type="button"
                                                    id="category-context-menu-{{ $customer->cust_id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false" aria-label="More"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="3" height="13"
                                                        fill="currentColor">
                                                        <path
                                                            d="M1.5,8C0.7,8,0,7.3,0,6.5S0.7,5,1.5,5S3,5.7,3,6.5S2.3,8,1.5,8z M1.5,3C0.7,3,0,2.3,0,1.5S0.7,0,1.5,0 S3,0.7,3,1.5S2.3,3,1.5,3z M1.5,10C2.3,10,3,10.7,3,11.5S2.3,13,1.5,13S0,12.3,0,11.5S0.7,10,1.5,10z">
                                                        </path>
                                                    </svg></button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="category-context-menu-{{ $customer->cust_id }}">
                                                    <li><a class="dropdown-item" href="{{ route('management.customers.show', $customer->cust_id) }}">View Details</a></li>
                                                    <li>
                                                        <form action="{{ route('management.customers.update-status', $customer->cust_id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="{{ $customer->cust_status == 1 ? 0 : 1 }}">
                                                            <button type="submit" class="dropdown-item text-{{ $customer->cust_status == 1 ? 'danger' : 'success' }}">
                                                                {{ $customer->cust_status == 1 ? 'Block' : 'Unblock' }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
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