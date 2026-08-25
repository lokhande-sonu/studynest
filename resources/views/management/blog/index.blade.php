@extends('management.layout.app')
@section('title', 'Blog Management')

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">

            @include('management.layout.flash-messages')

            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">Blog Management</h4>
                        <p class="mt-2">Manage all your blog posts here</p>
                    </div>
                    <div class="col-auto d-flex">
                        <a href="{{ route('management.blogs.create') }}" class="btn btn-primary">
                            Add New Blog
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-4">
                    <input type="text" placeholder="Start typing to search for blogs"
                           class="form-control form-control--search mx-auto" id="table-search"/>
                </div>

                <div class="sa-divider"></div>

                <table class="sa-datatables-init" data-order='[[ 0, "desc" ]]'
                       data-sa-search-input="#table-search">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="w-min" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach ($blogs as $blog)
                        <tr>
                            <td>{{ $blog->b_id }}</td>

                            <td>
                                @if($blog->b_image)
                                <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg">
                                    <img src="{{ asset('uploads/blog-photos/' . $blog->b_image) }}"
                                         width="40" height="40" style="object-fit: cover;" alt="Blog Image">
                                </div>
                                @else
                                <span class="text-muted">No Image</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('management.blogs.edit', $blog->b_id) }}" class="text-reset">
                                    {{ Str::limit($blog->b_title, 40) }}
                                </a>
                            </td>

                            <td>
                                @if ($blog->b_status == 1)
                                    <div class="badge badge-sa-success">Active</div>
                                @else
                                    <div class="badge badge-sa-danger">Inactive</div>
                                @endif
                            </td>

                            <td>{{ $blog->created_at->format('d M, Y') }}</td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sa-muted btn-sm" type="button"
                                        id="blog-menu-{{ $blog->b_id }}"
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
                                        aria-labelledby="blog-menu-{{ $blog->b_id }}">
                            
                                        <li>
                                            <a class="dropdown-item" href="{{ route('management.blogs.edit', $blog->b_id) }}">
                                                Edit
                                            </a>
                                        </li>
                            
                                        <li>
                                            <form method="POST"
                                                  action="{{ route('management.blogs.update-status', $blog->b_id) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="dropdown-item">
                                                    {{ $blog->b_status == 1 ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        <li>
                                            <form action="{{ route('management.blogs.destroy', $blog->b_id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this blog?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Delete</button>
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
@endsection
