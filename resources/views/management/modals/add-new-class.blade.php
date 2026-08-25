<!-- Add New Class Modal -->
<div class="modal fade" id="addNewClassModal" tabindex="-1"
     aria-labelledby="addNewClassModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('management.classes.store') }}"
                      method="POST">
                    @csrf

                    <div class="row">
                        <!-- Class Name -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Class Name</label>
                                <input type="text" name="class_name"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <select name="class_status" class="form-select">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="submit" class="btn btn-primary">
                            Save Class
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
