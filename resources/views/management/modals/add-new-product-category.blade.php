<!-- Add New Category Modal -->
<div class="modal fade" id="addNewCategoryModal" tabindex="-1"
     aria-labelledby="addNewCategoryModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
            </div>

            <div class="modal-body">
                <form action="{{ route('management.categories.store') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Category Name -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="cat_name"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- Category Photo -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Category Photo</label>
                                <input type="file" name="cat_photo"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- Visibility -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Visibility Status</label>
                                <select name="cat_status" class="form-select">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="submit" class="btn btn-primary">
                            Save Category
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
