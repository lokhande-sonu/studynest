@extends('management.layout.app')
@section('title', 'Add New Product')

@section('content')

<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">
            @include('management.layout.flash-messages')
            <form action="{{ route('management.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="py-5">
                    <div class="row g-4 align-items-center">
                        <div class="col">
                            <h4 class="h4 m-0">Add New Product</h4>
                            <p class="mt-2">Form will be used to add new product in system</p>
                        </div>
                        <div class="col-auto d-flex">
                            <button type="submit" class="btn btn-primary">Save Product</button>
                        </div>
                    </div>
                </div>
                
                <div class="sa-entity-layout" data-sa-container-query="{&quot;920&quot;:&quot;sa-entity-layout--size--md&quot;,&quot;1100&quot;:&quot;sa-entity-layout--size--lg&quot;}">
                    <div class="sa-entity-layout__body">
                        <div class="sa-entity-layout__main">
                            
                            <!-- Target Audience -->
                            <div class="card mb-5">
                                <div class="card-body p-5">
                                    <div class="mb-5">
                                        <h2 class="mb-0 fs-exact-18">Target Audience</h2>
                                    </div>
                                    <div class="row g-4">
                                        <!-- School -->
                                        <div class="col-md-12">
                                            <div class="mb-2">
                                                <label class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="is_all_schools" id="is_all_schools" value="1" onchange="toggleSelect('schools', this)">
                                                    <span class="form-check-label">All Schools</span>
                                                </label>
                                            </div>
                                            <div id="schools_wrapper">
                                                <label class="form-label">Select Schools</label>
                                                <select name="schools[]" id="schools" class="sa-select2 form-select" multiple>
                                                    @foreach($schools as $school)
                                                        <option value="{{ $school->sch_id }}">{{ $school->sch_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Class -->
                                        <div class="col-md-12">
                                            <div class="mb-2">
                                                <label class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="is_all_classes" id="is_all_classes" value="1" onchange="toggleSelect('classes', this)">
                                                    <span class="form-check-label">All Classes</span>
                                                </label>
                                            </div>
                                            <div id="classes_wrapper">
                                                <label class="form-label">Select Classes</label>
                                                <select name="classes[]" id="classes" class="sa-select2 form-select" multiple>
                                                    @foreach($classes as $class)
                                                        <option value="{{ $class->class_id }}">{{ $class->class_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Subject -->
                                        <div class="col-md-12">
                                            <div class="mb-2">
                                                <label class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="is_all_subjects" id="is_all_subjects" value="1" onchange="toggleSelect('subjects', this)">
                                                    <span class="form-check-label">All Subjects</span>
                                                </label>
                                            </div>
                                            <div id="subjects_wrapper">
                                                <label class="form-label">Select Subjects</label>
                                                <select name="subjects[]" id="subjects" class="sa-select2 form-select" multiple>
                                                    @foreach($subjects as $subject)
                                                        <option value="{{ $subject->subject_id }}">{{ $subject->subject_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="card">
                                <div class="card-body p-5">
                                    <div class="mb-5">
                                        <h2 class="mb-0 fs-exact-18">Basic information</h2>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-4">
                                                <label class="form-label">Product Name</label>
                                                <input type="text" name="p_name" class="form-control @error('p_name') is-invalid @enderror" value="{{ old('p_name') }}" required />
                                                @error('p_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label class="form-label">Product Category</label>
                                                <select name="p_cat_id" class="sa-select2 form-select @error('p_cat_id') is-invalid @enderror">
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->cat_id }}" {{ old('p_cat_id') == $category->cat_id ? 'selected' : '' }}>{{ $category->cat_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('p_cat_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label class="form-label">Gender</label>
                                                <select name="p_gender" class="form-select @error('p_gender') is-invalid @enderror">
                                                    <option value="Not Applicable" {{ old('p_gender') == 'Not Applicable' ? 'selected' : '' }}>Not Applicable</option>
                                                    <option value="Unisex" {{ old('p_gender') == 'Unisex' ? 'selected' : '' }}>Unisex</option>
                                                    <option value="Boys" {{ old('p_gender') == 'Boys' ? 'selected' : '' }}>Boys</option>
                                                    <option value="Girls" {{ old('p_gender') == 'Girls' ? 'selected' : '' }}>Girls</option>
                                                </select>
                                                @error('p_gender')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label class="form-label">Brand</label>
                                                <input type="text" name="p_brand" class="form-control @error('p_brand') is-invalid @enderror" value="{{ old('p_brand') }}" />
                                                @error('p_brand')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label class="form-label">Product Tag</label>
                                                <select name="p_tag" class="sa-select2 form-select @error('p_tag') is-invalid @enderror">
                                                    <option value="">Select Tag</option>
                                                    <option value="Best Seller" {{ old('p_tag') == 'Best Seller' ? 'selected' : '' }}>Best Seller</option>
                                                    <option value="New Arrival" {{ old('p_tag') == 'New Arrival' ? 'selected' : '' }}>New Arrival</option>
                                                    <option value="Featured" {{ old('p_tag') == 'Featured' ? 'selected' : '' }}>Featured</option>
                                                </select>
                                                @error('p_tag')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-4">
                                                <label class="form-label">Product Full Description</label>
                                                <textarea name="p_full_desc" id="summernote"
                                                    class="form-control @error('p_full_desc') is-invalid @enderror"
                                                    rows="8">{{ old('p_full_desc') }}</textarea>
                                                 @error('p_full_desc')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div>
                                                <label class="form-label">Short Description</label>
                                                <input type="text" name="p_short_desc" class="form-control @error('p_short_desc') is-invalid @enderror" value="{{ old('p_short_desc') }}" required />
                                                 @error('p_short_desc')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <div class="mb-4">
                                                <label class="form-label">Product Images</label>
                                                <div id="image-inputs-container">
                                                    <div class="input-group mb-2">
                                                        <input type="file" name="p_photos[]" class="form-control" accept="image/*" required>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="addImageInput()">+ Add Another Image</button>
                                                <small class="text-muted d-block mt-2">Max 5 images total. First image will be cover.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-4">
                                                <label class="form-label">Status</label>
                                                <select name="p_status" class="form-select">
                                                    <option value="1">Active</option>
                                                    <option value="0">Disabled</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory -->
                            <div class="card mt-5">
                                <div class="card-body p-5">
                                    <div class="mb-5 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h2 class="mb-0 fs-exact-18">Product Variants & Stock Inventory</h2>
                                            <small class="text-muted">
                                                <strong>GST Info:</strong> <span class="text-success">Inclusive</span> = Price includes GST (shows ₹100, customer pays ₹100). 
                                                <span class="text-danger">Exclusive</span> = Price excludes GST (shows ₹100 + GST, customer pays ₹118 for 18% GST).
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="addInventoryRow()">+ Add Variant</button>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 15%;">Variant Name <span class="text-danger">*</span></th>
                                                    <th style="width: 15%;">SKU <span class="text-danger">*</span></th>
                                                    <th style="width: 10%;">Stock <span class="text-danger">*</span></th>
                                                    <th style="width: 12%;">Unit Price <span class="text-danger">*</span></th>
                                                    <th style="width: 12%;">Discounted Price</th>
                                                    <th style="width: 12%;">GST Rate (%)</th>
                                                    <th style="width: 14%;">GST Type</th>
                                                    <th style="width: 10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="inventory_table_body">
                                                <!-- Rows will be added here -->
                                                <tr>
                                                    <td><input type="text" name="variant_name[]" class="form-control" placeholder="e.g. Small, Red" required></td>
                                                    <td><input type="text" name="prod_sku[]" class="form-control" placeholder="SKU-001" required></td>
                                                    <td><input type="number" name="available_stock[]" class="form-control" min="0" value="0" required></td>
                                                    <td><input type="number" name="unit_price[]" class="form-control" min="0" step="0.01" value="0" required></td>
                                                    <td><input type="number" name="discounted_unit_price[]" class="form-control" min="0" step="0.01"></td>
                                                    <td>
                                                        <select name="gst_rate[]" class="form-select form-select-sm">
                                                            <option value="0">0%</option>
                                                            <option value="5">5%</option>
                                                            <option value="12">12%</option>
                                                            <option value="18" selected>18%</option>
                                                            <option value="28">28%</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="gst_type[]" class="form-select form-select-sm">
                                                            <option value="inclusive" selected>Inclusive</option>
                                                            <option value="exclusive">Exclusive</option>
                                                        </select>
                                                    </td>
                                                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    // Initialize Summernote
    $('#summernote').summernote({
        placeholder: 'Enter full product description...',
        tabsize: 2,
        height: 200,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    function addImageInput() {
        const container = document.getElementById('image-inputs-container');
        const inputCount = container.querySelectorAll('input[type="file"]').length;
        
        if (inputCount >= 5) {
            alert("You can add a maximum of 5 upload fields.");
            return;
        }

        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="file" name="p_photos[]" class="form-control" accept="image/*">
            <button type="button" class="btn btn-danger" onclick="this.closest('.input-group').remove()">X</button>
        `;
        container.appendChild(div);
    }

    function toggleSelect(id, checkbox) {
        const wrapper = document.getElementById(id + '_wrapper');
        const select = document.getElementById(id);
        
        if (checkbox.checked) {
            wrapper.style.display = 'none';
            // Optional: clear selection
            // $(select).val(null).trigger('change');
        } else {
            wrapper.style.display = 'block';
        }
    }

    function addInventoryRow() {
        const tbody = document.getElementById('inventory_table_body');
        const row = `
            <tr>
                <td><input type="text" name="variant_name[]" class="form-control" placeholder="e.g. Small, Red" required></td>
                <td><input type="text" name="prod_sku[]" class="form-control" placeholder="SKU" required></td>
                <td><input type="number" name="available_stock[]" class="form-control" min="0" value="0" required></td>
                <td><input type="number" name="unit_price[]" class="form-control" min="0" step="0.01" value="0" required></td>
                <td><input type="number" name="discounted_unit_price[]" class="form-control" min="0" step="0.01"></td>
                <td>
                    <select name="gst_rate[]" class="form-select form-select-sm">
                        <option value="0">0%</option>
                        <option value="5">5%</option>
                        <option value="12">12%</option>
                        <option value="18" selected>18%</option>
                        <option value="28">28%</option>
                    </select>
                </td>
                <td>
                    <select name="gst_type[]" class="form-select form-select-sm">
                        <option value="inclusive" selected>Inclusive</option>
                        <option value="exclusive">Exclusive</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    }

    function removeRow(btn) {
        const row = btn.closest('tr');
        if (document.querySelectorAll('#inventory_table_body tr').length > 1) {
            row.remove();
        } else {
            alert("At least one variant is required.");
        }
    }
</script>

    @endsection
