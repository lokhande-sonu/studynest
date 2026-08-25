<!-- Add New School Modal -->
<div class="modal fade" id="addNewSchoolModal" tabindex="-1"
     aria-labelledby="addNewSchoolModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New School</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('management.schools.store') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- School Name -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">School Name</label>
                                <input type="text" name="sch_name"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- School City -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">City</label>
                                <input type="text" name="sch_city"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- School Mobile -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="sch_mobile"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- School Email -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="sch_email"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- School Address -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Address</label>
                                <textarea name="sch_address" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                        
                        <!-- School Description -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">School Short Description</label>
                                <textarea name="sch_desc" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>

                        <!-- School Logo -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">School Logo</label>
                                <input type="file" name="sch_logo"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <select name="sch_status" class="form-select">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>

                        <!-- Verified Badge -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label d-block">Verified Badge</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="is_verified_add">
                                    <label class="form-check-label" for="is_verified_add">Show Verified Badge</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="submit" class="btn btn-primary">
                            Save School
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
