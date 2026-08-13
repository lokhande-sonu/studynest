<!-- Add New Subject Modal -->
<div class="modal fade" id="addNewSubjectModal" tabindex="-1"
     aria-labelledby="addNewSubjectModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('management.subjects.store') }}"
                      method="POST">
                    @csrf

                    <div class="row">
                        <!-- Subject Name -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Subject Name</label>
                                <input type="text" name="subject_name"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <select name="subject_status" class="form-select">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="submit" class="btn btn-primary">
                            Save Subject
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
