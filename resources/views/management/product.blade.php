@extends('management.layout.app')
@section('title', 'Products Management')

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">
            @include('management.layout.flash-messages')
            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">Product Management</h4>
                        <p class="mt-2">Manage list of all the products</p>
                    </div>

                    <div class="col-auto d-flex gap-2">
                        <a href="{{ route('management.products.export') }}" class="btn btn-secondary">
                            <i class="fas fa-download me-2"></i>Export Excel
                        </a>
                        <button class="btn btn-info text-white bulkImportBtn" 
                                data-action="{{ route('management.products.import') }}"
                                data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                            <i class="fas fa-upload me-2"></i>Import Excel
                        </button>
                        <a href="{{ route('management.products.create') }}" class="btn btn-primary">
                            Add New Product
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-4">
                    <input type="text"
                           placeholder="Start typing product name"
                           class="form-control form-control--search mx-auto"
                           id="table-search" />
                </div>

                <div class="sa-divider"></div>

                <table class="sa-datatables-init"
                       data-order='[[0, "desc"]]'
                       data-sa-search-input="#table-search">

                    <thead>
                        <tr>
                            <th class="w-min">ID</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Total Stock</th>
                            <th>Status</th>
                            <th class="w-min" data-orderable="false">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->p_id }}</td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg me-4">
                                        @if($product->p_photo && file_exists(public_path(env('PRODUCT_PHOTO') . '/' . $product->p_photo)))
                                            <img src="{{ asset(env('PRODUCT_PHOTO') . '/' . $product->p_photo) }}"
                                                 width="40" height="40" alt="">
                                        @else
                                            <div class="sa-symbol__text">{{ substr($product->p_name, 0, 2) }}</div>
                                        @endif
                                    </div>

                                    <div>
                                        <a href="{{ route('management.products.edit', $product->p_id) }}" class="text-reset">{{ $product->p_name }}</a>
                                        <div class="text-muted mt-n1">{{ $product->prod_sku ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $product->category->cat_name ?? 'N/A' }}</td>
                            <td>{{ $product->p_brand ?? '-' }}</td>

                            <td>
                                {{ $product->stockInventories->sum('available_stock') }}
                            </td>

                            <td>
                                @if ($product->p_status == 1)
                                    <div class="badge badge-sa-success">Active</div>
                                @else
                                    <div class="badge badge-sa-danger">Disabled</div>
                                @endif
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sa-muted btn-sm" type="button"
                                                    id="category-context-menu-1" data-bs-toggle="dropdown"
                                                    aria-expanded="false" aria-label="More"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="3" height="13"
                                                        fill="currentColor">
                                                        <path
                                                            d="M1.5,8C0.7,8,0,7.3,0,6.5S0.7,5,1.5,5S3,5.7,3,6.5S2.3,8,1.5,8z M1.5,3C0.7,3,0,2.3,0,1.5S0.7,0,1.5,0 S3,0.7,3,1.5S2.3,3,1.5,3z M1.5,10C2.3,10,3,10.7,3,11.5S2.3,13,1.5,13S0,12.3,0,11.5S0.7,10,1.5,10z">
                                                        </path>
                                                    </svg></button>

                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('management.products.edit', $product->p_id) }}">
                                               Edit
                                            </a>
                                        </li>

                                        <li>
                                            <form method="POST"
                                                  action="{{ route('management.products.destroy', $product->p_id) }}"
                                                  onsubmit="return confirm('Delete this product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item text-danger" type="submit">
                                                    Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>
@include('management.modals.bulk-import')

@section('scripts')
<script>
    $(document).on('click', '.bulkImportBtn', function() {
        let action = $(this).data('action');
        $('#bulkImportForm').attr('action', action);
    });
</script>
@endsection
@endsection