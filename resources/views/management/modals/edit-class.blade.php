<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1"
     aria-labelledby="editClassModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="editClassForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="class_id" id="edit_class_id">

                    <div class="row">
                        <!-- Class Name -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Class Name</label>
                                <input type="text" name="class_name" id="edit_class_name"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <select name="class_status" id="edit_class_status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="submit" class="btn btn-primary">
                            Update Class
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
