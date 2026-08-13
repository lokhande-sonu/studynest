@extends('management.layout.app')
@section('title', 'Pre-Booking Requests')

@section('content')

<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">
            @include('management.layout.flash-messages')

            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">Pre-Bookings</h4>
                        <p class="mt-2">Manage pre-booking requests for school essentials</p>
                    </div>
                    <div class="col-auto d-flex">
                        <a href="{{ route('management.prebooking.export') }}" class="btn btn-primary">
                            Export to Excel
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="p-4"><input type="text" placeholder="Search by Name, Mobile, School..."
                        class="form-control form-control--search mx-auto" id="table-search" /></div>
                <div class="sa-divider"></div>
                <table class="sa-datatables-init" data-order="[[ 0, &quot;desc&quot; ]]"
                    data-sa-search-input="#table-search">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Details</th>
                            <th>School & Class</th>
                            <th>Token Amount</th>
                            <th>Payment Status</th>
                            <th>Date</th>
                            <th class="w-min" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prebookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg me-4">
                                        <div class="sa-symbol__text">{{ substr($booking->customer_name, 0, 1) }}</div>
                                    </div>
                                    <div>{{ $booking->customer_name }}
                                        <div class="text-muted mt-n1">{{ $booking->customer_mobile }} <br> {{ $booking->customer_email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-heading fw-medium">{{ $booking->school->sch_name ?? 'N/A' }}</div>
                                <div class="text-muted">{{ $booking->class->class_name ?? 'N/A' }}</div>
                            </td>
                            <td>₹{{ number_format($booking->token_amount, 2) }}</td>
                            <td>
                                @if($booking->payment_status == 'paid')
                                    <div class="badge badge-sa-success">Paid</div>
                                @else
                                    <div class="badge badge-sa-warning">Pending</div>
                                @endif
                            </td>
                            <td>{{ $booking->created_at->format('d/m/Y h:i A') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sa-muted btn-sm" type="button" id="booking-context-menu-{{ $booking->id }}" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More"><svg xmlns="http://www.w3.org/2000/svg" width="3" height="13" fill="currentColor"><path d="M1.5,8C0.7,8,0,7.3,0,6.5S0.7,5,1.5,5S3,5.7,3,6.5S2.3,8,1.5,8z M1.5,3C0.7,3,0,2.3,0,1.5S0.7,0,1.5,0 S3,0.7,3,1.5S2.3,3,1.5,3z M1.5,10C2.3,10,3,10.7,3,11.5S2.3,13,1.5,13S0,12.3,0,11.5S0.7,10,1.5,10z"></path></svg></button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="booking-context-menu-{{ $booking->id }}">
                                        @if($booking->payment_status != 'paid')
                                        <li>
                                            <form action="{{ route('management.prebooking.status', $booking->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="payment_status" value="paid">
                                                <button class="dropdown-item" type="submit">Mark as Paid</button>
                                            </form>
                                        </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('management.prebooking.status', $booking->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="payment_status" value="pending">
                                                <button class="dropdown-item text-danger" type="submit">Mark as Pending</button>
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
