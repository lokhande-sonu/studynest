@extends('management.layout.app') {{-- your layout file --}}
@section('title', 'Admin Profile')

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="py-5">
                <div class="row g-4 align-items-center">
                    <div class="col">
                        <h4 class="h4 m-0">Admin Profile</h4>
                        <p class="mt-2">Manage your profile details and password</p>
                    </div>
                </div>
            </div>

            <div class="sa-entity-layout" data-sa-container-query='{"920":"sa-entity-layout--size--md"}'>
                <div class="sa-entity-layout__body">
                    {{-- Sidebar --}}
                    <div class="sa-entity-layout__sidebar">
                        <div class="card">
                            <div class="card-body d-flex flex-column align-items-center">
                                <div class="pt-3">
                                    <div class="sa-symbol sa-symbol--shape--circle" style="--sa-symbol--size:6rem">
                                        <img src="{{ $admin->profile_photo ? asset('uploads/management-profile-photo/'.$admin->profile_photo) : asset('assets/images/avatar.png') }}" width="96" height="96" alt="Profile Photo">
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <div class="fs-exact-16 fw-medium">{{ $admin->name }}</div>
                                    <div class="fs-exact-13 text-muted mt-1">{{ $admin->email }}</div>
                                    <div class="fs-exact-13 text-muted mt-1">{{ $admin->mobile }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main --}}
                    <div class="sa-entity-layout__main">
                        {{-- Update Profile --}}
                        <div class="card mt-5">
                            <div class="card-body px-5 d-flex align-items-center justify-content-between">
                                <h2 class="mb-0 fs-exact-18 me-4">Update Profile Details</h2>
                                <hr/>
                            </div>
                            <form action="{{ route('management.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row px-5">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="mobile" class="form-control" value="{{ $admin->mobile }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" name="profile_photo" class="form-control">
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <button type="submit" class="btn btn-primary w-100">Update Details</button>
                                    </div>
                                </div>
                            </form>

                            <div class="sa-divider my-5"></div>

                            {{-- Update Password --}}
                            <div class="card-body px-5 py-4 d-flex align-items-center justify-content-between">
                                <h2 class="mb-0 fs-exact-18 me-4">Update Account Password</h2>
                                <hr/>
                            </div>
                            <form action="{{ route('management.profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row px-5">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="new_password_confirmation" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
