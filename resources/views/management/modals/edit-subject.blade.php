<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1"
     aria-labelledby="editSubjectModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="editSubjectForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="subject_id" id="edit_subject_id">

                    <div class="row">
                        <!-- Subject Name -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Subject Name</label>
                                <input type="text" name="subject_name" id="edit_subject_name"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <select name="subject_status" id="edit_subject_status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="submit" class="btn btn-primary">
                            Update Subject
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
