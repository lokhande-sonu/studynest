@extends('management.layout.app')
@section('title', 'Product Category Management')

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">

            @include('management.layout.flash-messages')

            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">Category Management</h4>
                        <p class="mt-2">Manage list of all the  product categories</p>
                    </div>
                    <div class="col-auto d-flex">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewCategoryModal">
                            Add New Category
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-4">
                    <input type="text" placeholder="Start typing to search for categories"
                           class="form-control form-control--search mx-auto" id="table-search"/>
                </div>

                <div class="sa-divider"></div>

                <table class="sa-datatables-init" data-order='[[ 1, "asc" ]]'
                       data-sa-search-input="#table-search">
                    <thead>
                        <tr>
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Products</th>
                            <th>Visibility Status</th>
                            <th class="w-min" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->cat_id }}</td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg me-4">
                                        <img src="{{ asset(env('PRODUCT_CATEGORY_PHOTO') . '/' . $category->cat_photo) }}"
                                             width="40" height="40" alt="">
                                    </div>
                                    <div>{{ $category->cat_name }}</div>
                                </div>
                            </td>

                            <td>{{ $category->products_count ?? 0 }}</td>

                            <td>
                                @if ($category->cat_status == 1)
                                    <div class="badge badge-sa-success">Active</div>
                                @else
                                    <div class="badge badge-sa-danger">Disabled</div>
                                @endif
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sa-muted btn-sm" type="button"
                                        id="category-menu-{{ $category->cat_id }}"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="3" height="13"
                                                fill="currentColor">
                                            <path d="M1.5,8C0.7,8,0,7.3,0,6.5S0.7,5,1.5,5S3,5.7,3,6.5S2.3,8,1.5,8z 
                                                    M1.5,3C0.7,3,0,2.3,0,1.5S0.7,0,1.5,0 
                                                    S3,0.7,3,1.5S2.3,3,1.5,3z 
                                                    M1.5,10C2.3,10,3,10.7,3,11.5 
                                                    S2.3,13,1.5,13S0,12.3,0,11.5 
                                                    S0.7,10,1.5,10z"></path>
                                        </svg>
                                    </button>
                            
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="category-menu-{{ $category->cat_id }}">
                            
                                        {{-- Edit --}}
                                        <li>
                                            <a class="dropdown-item editCategoryBtn"
                                                href="#"
                                                data-id="{{ $category->cat_id }}"
                                                data-name="{{ $category->cat_name }}"
                                                data-photo="{{ $category->cat_photo }}"
                                                data-status="{{ $category->cat_status }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCategoryModal">
                                                Edit
                                            </a>
                                        </li>
                            
                                        {{-- Toggle Status --}}
                                        <li>
                                            <form method="POST"
                                                  action="{{ route('management.categories.update-status', $category->cat_id) }}">
                                                @csrf
                                                @method('PUT')
                                                <button class="dropdown-item" type="submit">
                                                    {{ $category->cat_status == 1 ? 'Mark as Disabled' : 'Mark as Active' }}
                                                </button>
                                            </form>
                                        </li>
                            
                                        {{-- Delete --}}
                                        <li>
                                            <form method="POST"
                                                action="{{ route('management.categories.destroy', $category->cat_id) }}"
                                                onsubmit="return confirm('Delete this category?');">
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

@include('management.modals.add-new-product-category')
@include('management.modals.edit-product-category')




@endsection
