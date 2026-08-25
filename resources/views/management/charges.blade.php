@extends('management.layout.app') {{-- your layout file --}}
@section('title', 'Charges Management')

@section('content')

<div id="top" class="sa-app__body">
                <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
                    <div class="container">
                        @include('management.layout.flash-messages')
                        <div class="py-5">
                            <div class="row g-4 align-items-center">
                                <div class="col">

                                    <h4 class="h4 m-0">Charges Setting</h4>
                                    <p class="mt-2">Manage all the charges applicable for order purchase. All the active charges will be applicable on subtotal amount.</p>
                                </div>
                                <div class="col-auto d-flex"><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewChargeModal">Add New
                                        Charge</button></div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="p-4"><input type="text" placeholder="Start typing to search for charges"
                                    class="form-control form-control--search mx-auto" id="table-search" /></div>
                            <div class="sa-divider"></div>
                            <table class="sa-datatables-init" data-order="[[ 1, &quot;asc&quot; ]]"
                                data-sa-search-input="#table-search">
                                <thead>
                                    <tr>
                                        <th>Charge ID</th>
                                        <th>Charge Name</th>
                                        <th>Charge Type (%/₹)</th>
                                        <th>Charge Value</th>
                                        <th>Applicability Status</th>
                                        <th class="w-min" data-orderable="false">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($charges as $charge)
                                    <tr>
                                        <td>{{ $charge->charge_id }}</td>
                                        <td>{{ $charge->charge_name }}</td>
                                        <td>{{ ucfirst($charge->charge_type) }}</td>
                                        <td>{{ $charge->charge_value }}</td>
                                        <td>
                                            @if($charge->charge_status == 1)
                                                <div class="badge badge-sa-success">Active</div>
                                            @else
                                                <div class="badge badge-sa-danger">Disabled</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown"><button class="btn btn-sa-muted btn-sm" type="button"
                                                    id="charge-context-menu-{{ $charge->charge_id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false" aria-label="More"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="3" height="13"
                                                        fill="currentColor">
                                                        <path
                                                            d="M1.5,8C0.7,8,0,7.3,0,6.5S0.7,5,1.5,5S3,5.7,3,6.5S2.3,8,1.5,8z M1.5,3C0.7,3,0,2.3,0,1.5S0.7,0,1.5,0 S3,0.7,3,1.5S2.3,3,1.5,3z M1.5,10C2.3,10,3,10.7,3,11.5S2.3,13,1.5,13S0,12.3,0,11.5S0.7,10,1.5,10z">
                                                        </path>
                                                    </svg></button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="charge-context-menu-{{ $charge->charge_id }}">
                                                    <!-- Edit Button (trigger modal - requires JS implementation for population) -->
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editChargeModal{{ $charge->charge_id }}">Edit</a></li>
                                                    
                                                    <!-- Toggle Status -->
                                                    <li>
                                                        <form action="{{ route('management.charges.update-status', $charge->charge_id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="{{ $charge->charge_status == 1 ? 0 : 1 }}">
                                                            <button type="submit" class="dropdown-item">{{ $charge->charge_status == 1 ? 'Disable' : 'Enable' }}</button>
                                                        </form>
                                                    </li>

                                                    <!-- Delete -->
                                                    <li>
                                                        <form action="{{ route('management.charges.destroy', $charge->charge_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                        </form>
                                                    </li>

                                                </ul>
                                            </div>
                                            @include('management.modals.edit-charge')
                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @include('management.modals.add-charge')
            


@endsection            