<div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkImportModalLabel">Bulk Import</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="bulkImportForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label fw-bold">Select Excel/CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="import_file" name="import_file" required accept=".xlsx,.xls,.csv">
                        <div class="form-text mt-2">
                           <i class="fas fa-info-circle me-1"></i>
                           Please use the exported file format for best results. Existing records will be updated if the ID matches.
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-images me-2 text-primary"></i>Product Images (Optional)
                        </label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="card-text text-muted mb-3">
                                    Upload product images to be linked with your Excel data. Add image filenames in the "Image Filenames" column (Column R) of your Excel file.
                                </p>

                                <div class="mb-3">
                                    <label for="product_images" class="form-label">
                                        <i class="fas fa-upload me-1"></i>Upload Image Files
                                    </label>
                                    <input type="file" class="form-control" id="product_images" name="product_images[]" multiple accept="image/jpeg,image/png,image/gif,image/webp">
                                    <div class="form-text">Select multiple images (JPG, PNG, GIF, WEBP). Filenames must match the "Image Filenames" column in your Excel.</div>
                                </div>

                                <div class="text-center my-2 text-muted">
                                    <span class="text-uppercase small fw-bold">— OR —</span>
                                </div>

                                <div class="mb-2">
                                    <label for="image_zip" class="form-label">
                                        <i class="fas fa-file-archive me-1"></i>Upload ZIP Archive
                                    </label>
                                    <input type="file" class="form-control" id="image_zip" name="image_zip" accept=".zip">
                                    <div class="form-text">Upload a ZIP file containing all product images. All image files inside will be extracted and matched by filename.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-start" role="alert">
                        <i class="fas fa-lightbulb me-2 mt-1"></i>
                        <div>
                            <strong>How it works:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                <li>Export products to see the format with "Image URLs" and "Image Filenames" columns</li>
                                <li>In Excel, add image filenames (e.g., <code>product1.jpg,product2.png</code>) in Column R</li>
                                <li>Upload the same image files here along with your Excel file</li>
                                <li>Images will be automatically linked to products during import</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import me-1"></i>Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
