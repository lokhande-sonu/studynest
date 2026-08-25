@extends('management.layout.app')
@section('title', 'Class Management')

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">

            @include('management.layout.flash-messages')

            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">Class Management</h4>
                        <p class="mt-2">Manage list of all the classes</p>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <a href="{{ route('management.classes.export') }}" class="btn btn-secondary">
                            <i class="fas fa-download me-2"></i>Export Excel
                        </a>
                        <button class="btn btn-info text-white bulkImportBtn" 
                                data-action="{{ route('management.classes.import') }}"
                                data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                            <i class="fas fa-upload me-2"></i>Import Excel
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewClassModal">
                            Add New Class
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-4">
                    <input type="text" placeholder="Start typing to search for classes"
                           class="form-control form-control--search mx-auto" id="table-search"/>
                </div>

                <div class="sa-divider"></div>

                <table class="sa-datatables-init" data-order='[[ 0, "desc" ]]'
                       data-sa-search-input="#table-search">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Class Name</th>
                            <th>Status</th>
                            <th class="w-min" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach ($classes as $class)
                        <tr>
                            <td>{{ $class->class_id }}</td>

                            <td>{{ $class->class_name }}</td>

                            <td>
                                @if ($class->class_status == 1)
                                    <div class="badge badge-sa-success">Active</div>
                                @else
                                    <div class="badge badge-sa-danger">Disabled</div>
                                @endif
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sa-muted btn-sm" type="button"
                                        id="class-menu-{{ $class->class_id }}"
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
                                        aria-labelledby="class-menu-{{ $class->class_id }}">
                            
                                        {{-- Edit --}}
                                        <li>
                                            <a class="dropdown-item editClassBtn"
                                                href="#"
                                                data-id="{{ $class->class_id }}"
                                                data-name="{{ $class->class_name }}"
                                                data-status="{{ $class->class_status }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editClassModal">
                                                Edit
                                            </a>
                                        </li>
                            
                                        {{-- Delete --}}
                                        <li>
                                            <form method="POST"
                                                action="{{ route('management.classes.destroy', $class->class_id) }}"
                                                onsubmit="return confirm('Delete this class?');">
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

@include('management.modals.add-new-class')
@include('management.modals.edit-class')
@include('management.modals.bulk-import')

@section('scripts')
<script>
    $(document).on('click', '.editClassBtn', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let status = $(this).data('status');

        $('#edit_class_id').val(id);
        $('#edit_class_name').val(name);
        $('#edit_class_status').val(status);

        // Update form action
        $('#editClassForm').attr('action', "{{ url('management/classes') }}/" + id);
    });
    $(document).on('click', '.bulkImportBtn', function() {
        let action = $(this).data('action');
        $('#bulkImportForm').attr('action', action);
    });
</script>
@endsection
@endsection
