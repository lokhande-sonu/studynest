@extends('management.layout.app')
@section('title', 'School Management')

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">

            @include('management.layout.flash-messages')

            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">School Management</h4>
                        <p class="mt-2">Manage list of all the schools</p>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <a href="{{ route('management.schools.export') }}" class="btn btn-secondary">
                            <i class="fas fa-download me-2"></i>Export Excel
                        </a>
                        <button class="btn btn-info text-white bulkImportBtn" 
                                data-action="{{ route('management.schools.import') }}"
                                data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                            <i class="fas fa-upload me-2"></i>Import Excel
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewSchoolModal">
                            Add New School
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-4">
                    <input type="text" placeholder="Start typing to search for schools"
                           class="form-control form-control--search mx-auto" id="table-search"/>
                </div>

                <div class="sa-divider"></div>

                <table class="sa-datatables-init" data-order='[[ 0, "desc" ]]'
                       data-sa-search-input="#table-search">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>School Name</th>
                            <th>Contact Info</th>
                            <th>City</th>
                            <th>Verified</th>
                            <th>Status</th>
                            <th class="w-min" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach ($schools as $school)
                        <tr>
                            <td>{{ $school->sch_id }}</td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg me-4">
                                        <img src="{{ asset('uploads/school-logo/' . $school->sch_logo) }}"
                                             width="40" height="40" alt="">
                                    </div>
                                    <div>{{ $school->sch_name }}</div>
                                </div>
                            </td>

                            <td>
                                <div>{{ $school->sch_mobile }}</div>
                                <div class="text-muted">{{ $school->sch_email }}</div>
                            </td>

                            <td>{{ $school->sch_city }}</td>

                            <td>
                                @if ($school->is_verified == 1)
                                    <div class="badge badge-sa-info">Verified</div>
                                @else
                                    <div class="badge badge-sa-secondary">Not Verified</div>
                                @endif
                            </td>

                            <td>
                                @if ($school->sch_status == 1)
                                    <div class="badge badge-sa-success">Active</div>
                                @else
                                    <div class="badge badge-sa-danger">Disabled</div>
                                @endif
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sa-muted btn-sm" type="button"
                                        id="school-menu-{{ $school->sch_id }}"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="3" height="13"
                                                fill="currentColor">
                                            <path d="M1.5,8C0.7,8,0,7.3,0,6.5S0.7,5,1.5,5S3,5.7,3,6.5S2.3,8,1.5,8z 
                                                    M1.5,3C0.7,3,0,2.3,0,1.5S0.7,0,1.5,0 
                                                    S3,0.7,3,1.5S2.3,3,1.5,3z 
                                                    M1.5,10C2.3,10,3,10.7,3,11.5 
                                                    S2.3,13,1.5,13S0,12.3,0,11.5 
                                                    S0.7,10,1.5,10z"></path>
                                        </svg>
                                    </button>
                            
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="school-menu-{{ $school->sch_id }}">
                            
                                        {{-- Edit --}}
                                        <li>
                                            <a class="dropdown-item editSchoolBtn"
                                                href="#"
                                                data-id="{{ $school->sch_id }}"
                                                data-name="{{ $school->sch_name }}"
                                                data-mobile="{{ $school->sch_mobile }}"
                                                data-email="{{ $school->sch_email }}"
                                                data-address="{{ $school->sch_address }}"
                                                data-desc="{{ $school->sch_desc }}"
                                                data-city="{{ $school->sch_city }}"
                                                data-is_verified="{{ $school->is_verified }}"
                                                data-logo="{{ $school->sch_logo }}"
                                                data-status="{{ $school->sch_status }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSchoolModal">
                                                Edit
                                            </a>
                                        </li>
                            
                                        {{-- Delete --}}
                                        <li>
                                            <form method="POST"
                                                action="{{ route('management.schools.destroy', $school->sch_id) }}"
                                                onsubmit="return confirm('Delete this school?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item text-danger" type="submit">
                                                    Delete
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

@include('management.modals.add-new-school')
@include('management.modals.edit-school')
@include('management.modals.bulk-import')

@section('scripts')
<script>
    $(document).on('click', '.editSchoolBtn', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let mobile = $(this).data('mobile');
        let email = $(this).data('email');
        let address = $(this).data('address');
        let desc = $(this).data('desc');
        let city = $(this).data('city');
        let isVerified = $(this).data('is_verified');
        let logo = $(this).data('logo');
        let status = $(this).data('status');

        let assetBase = "{{ asset('uploads/school-logo') }}";

        $('#edit_sch_id').val(id);
        $('#edit_sch_name').val(name);
        $('#edit_sch_mobile').val(mobile);
        $('#edit_sch_email').val(email);
        $('#edit_sch_address').val(address);
        $('#edit_sch_desc').val(desc);
        $('#edit_sch_city').val(city);
        $('#edit_is_verified').prop('checked', isVerified == 1);
        $('#edit_sch_status').val(status);

        // Update form action
        $('#editSchoolForm').attr('action', "{{ url('management/schools') }}/" + id);

        // Update image preview
        $('#edit_sch_logo_preview').attr('src', assetBase + '/' + logo);
    });
    $(document).on('click', '.bulkImportBtn', function() {
        let action = $(this).data('action');
        $('#bulkImportForm').attr('action', action);
    });
</script>
@endsection
@endsection
