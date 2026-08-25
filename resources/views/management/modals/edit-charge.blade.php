<!-- Edit Modal for this item (Naive approach: repeating modal. Ideally use single modal + JS) -->
                                            <div class="modal fade" id="editChargeModal{{ $charge->charge_id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('management.charges.update', $charge->charge_id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Charge</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Charge Name</label>
                                                                    <input type="text" class="form-control" name="charge_name" value="{{ $charge->charge_name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Type</label>
                                                                    <select class="form-select" name="charge_type">
                                                                        <option value="percentage" {{ $charge->charge_type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                                                        <option value="fixed" {{ $charge->charge_type == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Value</label>
                                                                    <input type="number" step="0.01" class="form-control" name="charge_value" value="{{ $charge->charge_value }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Status</label>
                                                                    <select class="form-select" name="charge_status">
                                                                        <option value="1" {{ $charge->charge_status == 1 ? 'selected' : '' }}>Active</option>
                                                                        <option value="0" {{ $charge->charge_status == 0 ? 'selected' : '' }}>Disabled</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>