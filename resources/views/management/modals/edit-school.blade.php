<!-- Edit School Modal -->
<div class="modal fade" id="editSchoolModal" tabindex="-1"
     aria-labelledby="editSchoolModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit School</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="editSchoolForm" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="sch_id" id="edit_sch_id">

                    <div class="row">
                        <!-- School Name -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">School Name</label>
                                <input type="text" name="sch_name" id="edit_sch_name"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- School City -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">City</label>
                                <input type="text" name="sch_city" id="edit_sch_city"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- School Mobile -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="sch_mobile" id="edit_sch_mobile"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- School Email -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="sch_email" id="edit_sch_email"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- School Address -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Address</label>
                                <textarea name="sch_address" id="edit_sch_address"
                                          class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                        
                         <!-- School Descrition -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">School Short Description</label>
                                <textarea name="sch_desc" id="edit_sch_desc"
                                          class="form-control" rows="4" required></textarea>
                            </div>
                        </div>

                        <!-- Current Logo -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Current Logo</label>
                                <div class="border p-4 d-flex justify-content-center">
                                    <div class="max-w-20x">
                                        <img id="edit_sch_logo_preview"
                                             src=""
                                             class="w-100 h-auto"
                                             width="60" height="60" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New Logo -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Change Logo</label>
                                <input type="file" name="sch_logo"
                                       class="form-control">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <select name="sch_status" id="edit_sch_status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>

                        <!-- Verified Badge -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label d-block">Verified Badge</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="edit_is_verified">
                                    <label class="form-check-label" for="edit_is_verified">Show Verified Badge</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="submit" class="btn btn-primary">
                            Update School
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
