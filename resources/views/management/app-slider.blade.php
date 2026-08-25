@extends('management.layout.app')
@section('title', 'App Slider Management')

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">

            @include('management.layout.flash-messages')

            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">App Slider Management</h4>
                        <p class="mt-2">Manage list of all the app sliders</p>
                    </div>
                    <div class="col-auto d-flex">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSliderModal">
                            Add New Slide
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-4">
                    <input type="text" placeholder="Start typing to search for sliders"
                           class="form-control form-control--search mx-auto" id="table-search"/>
                </div>

                <div class="sa-divider"></div>

                <table class="sa-datatables-init" data-order='[[ 0, "desc" ]]'
                       data-sa-search-input="#table-search">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Slider Image</th>
                            <th>Status</th>
                            <th class="w-min" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach ($sliders as $slider)
                        <tr>
                            <td>{{ $slider->id }}</td>

                            <td>
                                <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg">
                                    <img src="{{ asset('uploads/app-sliders/' . $slider->slider_photo_url) }}"
                                         width="80" height="40" style="object-fit: cover;" alt="Slider">
                                </div>
                            </td>

                            <td>
                                @if ($slider->status == 1)
                                    <div class="badge badge-sa-success">Active</div>
                                @else
                                    <div class="badge badge-sa-danger">Inactive</div>
                                @endif
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sa-muted btn-sm" type="button"
                                        id="slider-menu-{{ $slider->id }}"
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
                                        aria-labelledby="slider-menu-{{ $slider->id }}">
                            
                                        {{-- Edit --}}
                                        <li>
                                            <a class="dropdown-item edit-slider-btn"
                                                href="#"
                                                data-id="{{ $slider->id }}"
                                                data-status="{{ $slider->status }}"
                                                data-image="{{ asset('uploads/app-sliders/' . $slider->slider_photo_url) }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSliderModal">
                                                Edit
                                            </a>
                                        </li>
                            
                                        {{-- Toggle Status --}}
                                        <li>
                                            <form method="POST"
                                                  action="{{ route('management.app-slider.update-status', $slider->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="{{ $slider->status == 1 ? 0 : 1 }}">
                                                <button class="dropdown-item" type="submit">
                                                    {{ $slider->status == 1 ? 'Mark as Inactive' : 'Mark as Active' }}
                                                </button>
                                            </form>
                                        </li>
                            
                                        {{-- Delete --}}
                                        <li>
                                            <form method="POST"
                                                action="{{ route('management.app-slider.destroy', $slider->id) }}"
                                                onsubmit="return confirm('Delete this slider?');">
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

<!-- Add New Slider Modal -->
<div class="modal fade" id="addSliderModal" tabindex="-1" aria-labelledby="addSliderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSliderModalLabel">Add New Slide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('management.app-slider.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="image" class="form-label">Slide Image</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                        @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                        <div class="mt-2 text-center">
                            <img src="" alt="Preview" class="img-fluid" style="max-height: 150px; display: none;" id="add-preview">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="modal-footer px-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Slide</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Slider Modal -->
<div class="modal fade" id="editSliderModal" tabindex="-1" aria-labelledby="editSliderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSliderModalLabel">Edit Slide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editSliderForm" action="#" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="edit_image" class="form-label">Slide Image (Leave empty to keep current)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="edit_image" name="image" accept="image/*">
                        @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                        <div class="mt-2 text-center">
                            <img src="" alt="Preview" class="img-fluid" style="max-height: 150px; display: none;" id="edit-preview">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="edit_status" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="modal-footer px-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Slide</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Preview image before upload for add form
    document.getElementById('image').addEventListener('change', function() {
        const preview = document.getElementById('add-preview');
        const file = this.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            
            reader.readAsDataURL(file);
        }
    });

    // Edit Slider
    $(document).on('click', '.edit-slider-btn', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const image = $(this).data('image');
        
        // Set form action
        const url = "{{ route('management.app-slider.update', ':id') }}";
        $('#editSliderForm').attr('action', url.replace(':id', id));
        
        // Set inputs
        $('#edit_status').val(status);
        $('#edit-preview').attr('src', image);
        $('#edit-preview').show();
    });

    // Preview image for edit form
    document.getElementById('edit_image').addEventListener('change', function() {
        const preview = document.getElementById('edit-preview');
        const file = this.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
