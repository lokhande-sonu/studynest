<!-- Add New Category modal -->
     <div class="modal fade" id="addNewChargeModal" tabindex="-1" aria-labelledby="addNewChargeModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Charge</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('management.charges.store') }}"
                      method="POST">
                    @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Charge Name</label>
                                    <input type="text" name="charge_name" class="form-control" value="" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Charge Type</label>
                                    <select name="charge_type" class="form-select">
                                        <option selected="" value="percentage">Percentage</option>
                                        <option value="fixed">Fixed Amount</option>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Charge Value</label>
                                    <input type="number" name="charge_value" class="form-control" value="" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Applicability Status</label>
                                    <select name="charge_status" class="form-select">
                                        <option selected="" value="1">Active</option>
                                        <option value="0">Disable</option>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Add Charge</button>
                                                            </div>
                    </form>    
                </div>
            </div>
        </div>
    </div>