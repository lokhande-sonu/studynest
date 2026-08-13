@extends('management.layout.app')
@section('title', 'Subject Management')

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">

            @include('management.layout.flash-messages')

            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">Subject Management</h4>
                        <p class="mt-2">Manage list of all the subjects</p>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <a href="{{ route('management.subjects.export') }}" class="btn btn-secondary">
                            <i class="fas fa-download me-2"></i>Export Excel
                        </a>
                        <button class="btn btn-info text-white bulkImportBtn" 
                                data-action="{{ route('management.subjects.import') }}"
                                data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                            <i class="fas fa-upload me-2"></i>Import Excel
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewSubjectModal">
                            Add New Subject
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-4">
                    <input type="text" placeholder="Start typing to search for subjects"
                           class="form-control form-control--search mx-auto" id="table-search"/>
                </div>

                <div class="sa-divider"></div>

                <table class="sa-datatables-init" data-order='[[ 0, "desc" ]]'
                       data-sa-search-input="#table-search">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subject Name</th>
                            <th>Status</th>
                            <th class="w-min" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->subject_id }}</td>

                            <td>{{ $subject->subject_name }}</td>

                            <td>
                                @if ($subject->subject_status == 1)
                                    <div class="badge badge-sa-success">Active</div>
                                @else
                                    <div class="badge badge-sa-danger">Disabled</div>
                                @endif
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sa-muted btn-sm" type="button"
                                        id="subject-menu-{{ $subject->subject_id }}"
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
                                        aria-labelledby="subject-menu-{{ $subject->subject_id }}">
                            
                                        {{-- Edit --}}
                                        <li>
                                            <a class="dropdown-item editSubjectBtn"
                                                href="#"
                                                data-id="{{ $subject->subject_id }}"
                                                data-name="{{ $subject->subject_name }}"
                                                data-status="{{ $subject->subject_status }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSubjectModal">
                                                Edit
                                            </a>
                                        </li>
                            
                                        {{-- Delete --}}
                                        <li>
                                            <form method="POST"
                                                action="{{ route('management.subjects.destroy', $subject->subject_id) }}"
                                                onsubmit="return confirm('Delete this subject?');">
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

@include('management.modals.add-new-subject')
@include('management.modals.edit-subject')
@include('management.modals.bulk-import')

@section('scripts')
<script>
    $(document).on('click', '.editSubjectBtn', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let status = $(this).data('status');

        $('#edit_subject_id').val(id);
        $('#edit_subject_name').val(name);
        $('#edit_subject_status').val(status);

        // Update form action
        $('#editSubjectForm').attr('action', "{{ url('management/subjects') }}/" + id);
    });
    $(document).on('click', '.bulkImportBtn', function() {
        let action = $(this).data('action');
        $('#bulkImportForm').attr('action', action);
    });
</script>
@endsection
@endsection
