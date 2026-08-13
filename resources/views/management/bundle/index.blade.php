@extends('management.layout.app')
@section('title', 'Bundle Management')

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">

            @include('management.layout.flash-messages')

            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">Bundle Management</h4>
                        <p class="mt-2">Manage bundles and their products</p>
                    </div>
                    <div class="col-auto d-flex">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewBundleModal">
                            Add New Bundle
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-4">
                    <input type="text" placeholder="Start typing to search for bundles"
                           class="form-control form-control--search mx-auto" id="table-search"/>
                </div>

                <div class="sa-divider"></div>

                <table class="sa-datatables-init" data-order='[[ 0, "desc" ]]'
                       data-sa-search-input="#table-search">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Bundle Info</th>
                            <th>Products Count</th>
                            <th>Status</th>
                            <th class="w-min" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach ($bundles as $bundle)
                        <tr>
                            <td>{{ $bundle->b_id }}</td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg me-4">
                                        <img src="{{ asset('uploads/bundle/' . $bundle->b_image) }}"
                                             width="40" height="40" alt="">
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $bundle->b_name }}</div>
                                        <div class="text-muted small">{{ Str::limit($bundle->b_short_desc, 50) }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge badge-sa-primary">{{ $bundle->products_count }} Products</span>
                            </td>

                            <td>
                                @if ($bundle->b_status == 1)
                                    <div class="badge badge-sa-success">Active</div>
                                @else
                                    <div class="badge badge-sa-danger">Disabled</div>
                                @endif
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sa-muted btn-sm" type="button"
                                        id="bundle-menu-{{ $bundle->b_id }}"
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
                                        aria-labelledby="bundle-menu-{{ $bundle->b_id }}">
                            
                                        {{-- Manage Products --}}
                                        <li>
                                            <a class="dropdown-item manageProductsBtn"
                                                href="#"
                                                data-id="{{ $bundle->b_id }}"
                                                data-name="{{ $bundle->b_name }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#manageBundleProductsModal">
                                                Manage Products
                                            </a>
                                        </li>

                                        {{-- Edit --}}
                                        <li>
                                            <a class="dropdown-item editBundleBtn"
                                                href="#"
                                                data-id="{{ $bundle->b_id }}"
                                                data-name="{{ $bundle->b_name }}"
                                                data-desc="{{ $bundle->b_short_desc }}"
                                                data-image="{{ $bundle->b_image }}"
                                                data-status="{{ $bundle->b_status }}"
                                                data-allschools="{{ $bundle->is_all_schools }}"
                                                data-allclasses="{{ $bundle->is_all_classes }}"
                                                data-allsubjects="{{ $bundle->is_all_subjects }}"
                                                data-schools="{{ json_encode($bundle->schools->pluck('sch_id')->toArray()) }}"
                                                data-classes="{{ json_encode($bundle->classes->pluck('class_id')->toArray()) }}"
                                                data-subjects="{{ json_encode($bundle->subjects->pluck('subject_id')->toArray()) }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editBundleModal">
                                                Edit
                                            </a>
                                        </li>
                            
                                        {{-- Delete --}}
                                        <li>
                                            <form method="POST"
                                                action="{{ route('management.bundles.destroy', $bundle->b_id) }}"
                                                onsubmit="return confirm('Delete this bundle?');">
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

@include('management.modals.add-new-bundle')
@include('management.modals.edit-bundle')
@include('management.modals.manage-bundle-products')

@section('scripts')
<script>
    // Use event delegation to handle clicks even after pagination or search
    $(document).on('click', '.editBundleBtn', function() {
        let btn = $(this);
        let id = btn.data('id');
        let name = btn.data('name');
        let desc = btn.data('desc');
        let image = btn.data('image');
        let status = btn.data('status');
        
        let allSchools = btn.data('allschools');
        let allClasses = btn.data('allclasses');
        let allSubjects = btn.data('allsubjects');

        let schools = btn.data('schools');
        let classes = btn.data('classes');
        let subjects = btn.data('subjects');

        let assetBase = "{{ asset('uploads/bundle') }}";

        $('#edit_b_id').val(id);
        $('#edit_b_name').val(name);
        $('#edit_b_short_desc').val(desc);
        $('#edit_b_status').val(status);

        // Update form action
        $('#editBundleForm').attr('action', "{{ url('management/bundles') }}/" + id);

        // Update image preview
        if (image) {
            $('#edit_b_image_preview').attr('src', assetBase + '/' + image).show();
        } else {
            $('#edit_b_image_preview').hide();
        }

        // Handle checkboxes for All/Select
        $('#edit_is_all_schools').prop('checked', !!allSchools);
        if(allSchools) {
            $('#edit_schools_wrapper').hide();
        } else {
            $('#edit_schools_wrapper').show();
        }

        $('#edit_is_all_classes').prop('checked', !!allClasses);
        if(allClasses) {
            $('#edit_classes_wrapper').hide();
        } else {
            $('#edit_classes_wrapper').show();
        }

        $('#edit_is_all_subjects').prop('checked', !!allSubjects);
        if(allSubjects) {
            $('#edit_subjects_wrapper').hide();
        } else {
            $('#edit_subjects_wrapper').show();
        }

        // Store these for the shown event
        $('#editBundleModal').data('schools', schools);
        $('#editBundleModal').data('classes', classes);
        $('#editBundleModal').data('subjects', subjects);
    });

    $(document).on('click', '.manageProductsBtn', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        
        $('#manage_bundle_id').val(id);
        $('#manage_bundle_title').text('Manage Products for: ' + name);
        
        // Reset state
        $('.product-checkbox').prop('checked', false);
        $('.product-options').hide();
        $('.product-options input, .product-options select').prop('disabled', true);
        $('.product-card-selectable').removeClass('border-primary shadow-sm');
        
        // Fetch current products
        $.ajax({
            url: "{{ url('management/bundles') }}/" + id + "/products",
            type: "GET",
            success: function(response) {
                if(response.products) {
                    response.products.forEach(function(item) {
                        let pid = item.product_id;
                        let checkbox = $('#product_check_' + pid);
                        checkbox.prop('checked', true);
                        
                        // Show and enable options
                        let options = $('#options_' + pid);
                        options.show();
                        options.find('input, select').prop('disabled', false);
                        
                        // Set values
                        $('#qty_input_' + pid).val(item.quantity);
                        $('#variant_select_' + pid).val(item.variant_id);
                        
                        // Style card
                        $('#product_card_' + pid).addClass('border-primary shadow-sm');
                    });
                }
            },
            error: function() {
                alert('Error fetching bundle products.');
            }
        });

        // Update form action
        $('#manageProductsForm').attr('action', "{{ url('management/bundles') }}/" + id + "/products");
    });

    $(document).on('change', '.product-checkbox', function() {
        let pid = $(this).val();
        let isChecked = $(this).is(':checked');
        let options = $('#options_' + pid);
        let card = $('#product_card_' + pid);

        if (isChecked) {
            options.stop().slideDown(200);
            options.find('input, select').prop('disabled', false);
            card.addClass('border-primary shadow-sm');
        } else {
            options.stop().slideUp(200);
            options.find('input, select').prop('disabled', true);
            card.removeClass('border-primary shadow-sm');
        }
    });

    // Make clicking the card (outside of links/inputs) toggle the checkbox
    $(document).on('click', '.product-card-selectable', function(e) {
        if ($(e.target).closest('input, select, .form-check-input').length === 0) {
            let checkbox = $(this).find('.product-checkbox');
            checkbox.prop('checked', !checkbox.is(':checked')).trigger('change');
        }
    });

    function toggleSelect(id, checkbox) {
        const wrapper = document.getElementById(id + '_wrapper');
        if (checkbox.checked) {
            $(wrapper).hide();
        } else {
            $(wrapper).show();
        }
    }

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        // Initialize Select2 when modal is shown
        $('#addNewBundleModal').on('shown.bs.modal', function () {
            $('#schools, #classes, #subjects').select2({
                dropdownParent: $('#addNewBundleModal'),
                width: '100%'
            });
        });

        $('#editBundleModal').on('shown.bs.modal', function () {
            let modal = $(this);
            let schools = modal.data('schools') || [];
            let classes = modal.data('classes') || [];
            let subjects = modal.data('subjects') || [];

            $('#edit_schools, #edit_classes, #edit_subjects').select2({
                dropdownParent: $('#editBundleModal'),
                width: '100%'
            });

            // Set values after select2 is ready
            $('#edit_schools').val(schools).trigger('change');
            $('#edit_classes').val(classes).trigger('change');
            $('#edit_subjects').val(subjects).trigger('change');
        });

        // Search in manage products modal
        $('#productSearchInput').on('keyup', function() {
            let filter = this.value.toLowerCase();
            $('.product-item').each(function() {
                let name = $(this).data('name');
                if (name.includes(filter)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>
@endsection
@endsection
