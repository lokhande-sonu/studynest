<div class="modal fade" id="addNewBundleModal" tabindex="-1" aria-labelledby="addNewBundleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewBundleModalLabel">Add New Bundle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('management.bundles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label">Bundle Name</label>
                        <input type="text" class="form-control" name="b_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea class="form-control" name="b_short_desc" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bundle Image</label>
                        <input type="file" class="form-control" name="b_image" accept="image/*" required onchange="previewImage(this, 'add_b_image_preview')">
                        <div class="mt-2">
                            <img id="add_b_image_preview" src="" width="100" style="display: none;" alt="Preview">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="b_status">
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>

                    <hr>
                    <h6>Applicability</h6>

                    <div class="row">
                        <!-- Schools -->
                        <div class="col-md-12 mb-3">
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

                        <!-- Classes -->
                        <div class="col-md-12 mb-3">
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

                        <!-- Subjects -->
                        <div class="col-md-12 mb-3">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Bundle</button>
                </div>
            </form>
        </div>
    </div>
</div>
