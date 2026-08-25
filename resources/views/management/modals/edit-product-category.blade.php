<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1"
     aria-labelledby="editCategoryModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
            </div>

            <div class="modal-body">
                <form id="editCategoryForm" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="cat_id" id="edit_cat_id">

                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="cat_name"
                                       id="edit_cat_name"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Visibility Status</label>
                                <select name="cat_status"
                                        id="edit_cat_status"
                                        class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>

                        <!-- Current Image -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Current Category Photo</label>
                                <div class="border p-4 d-flex justify-content-center">
                                    <div class="max-w-20x">
                                        <img id="edit_cat_photo_preview"
                                             src=""
                                             class="w-100 h-auto"
                                             width="60" height="60" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New Photo -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Change Category Photo</label>
                                <input type="file" name="cat_photo"
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="submit" class="btn btn-primary">
                            Update Category
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
