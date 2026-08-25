@extends('management.layout.app')
@section('title', 'Add New Blog')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
<div id="top" class="sa-app__body">
    <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
        <div class="container">
            @include('management.layout.flash-messages')
            <form action="{{ route('management.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="py-5">
                    <div class="row g-4 align-items-center">
                        <div class="col">
                            <h4 class="h4 m-0">Add New Blog</h4>
                            <p class="mt-2">Create a new blog post for your website</p>
                        </div>
                        <div class="col-auto d-flex">
                            <button type="submit" class="btn btn-primary">Save Blog</button>
                        </div>
                    </div>
                </div>

                <div class="sa-entity-layout" data-sa-container-query="{&quot;920&quot;:&quot;sa-entity-layout--size--md&quot;,&quot;1100&quot;:&quot;sa-entity-layout--size--lg&quot;}">
                    <div class="sa-entity-layout__body">
                        <div class="sa-entity-layout__main">
                            <div class="card">
                                <div class="card-body p-5">
                                    <div class="mb-5">
                                        <h2 class="mb-0 fs-exact-18">Basic information</h2>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Blog Title</label>
                                        <input type="text" name="b_title" class="form-control @error('b_title') is-invalid @enderror" value="{{ old('b_title') }}" required>
                                        @error('b_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Content</label>
                                        <textarea name="b_content" id="summernote" class="form-control @error('b_content') is-invalid @enderror" rows="12">{{ old('b_content') }}</textarea>
                                        @error('b_content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-5">
                                <div class="card-body p-5">
                                    <div class="mb-5">
                                        <h2 class="mb-0 fs-exact-18">Search engine optimization</h2>
                                        <div class="mt-2 text-muted">Provide information that will help improve the snippet and bring your blog to the top of search engines.</div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Page title</label>
                                        <input type="text" name="b_meta_title" class="form-control" value="{{ old('b_meta_title') }}">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Meta description</label>
                                        <textarea name="b_meta_description" class="form-control" rows="2">{{ old('b_meta_description') }}</textarea>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Keywords</label>
                                        <input type="text" name="b_meta_keywords" class="form-control" value="{{ old('b_meta_keywords') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sa-entity-layout__sidebar">
                            <div class="card w-100">
                                <div class="card-body p-5">
                                    <div class="mb-5">
                                        <h2 class="mb-0 fs-exact-18">Visibility</h2>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Status</label>
                                        <select name="b_status" class="form-select">
                                            <option value="1" {{ old('b_status') == 1 ? 'selected' : '' }}>Published</option>
                                            <option value="0" {{ old('b_status') == 0 ? 'selected' : '' }}>Draft</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card w-100 mt-5">
                                <div class="card-body p-5">
                                    <div class="mb-5">
                                        <h2 class="mb-0 fs-exact-18">Blog Image</h2>
                                    </div>
                                    <div class="mb-4 text-center">
                                        <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--xxl">
                                            <img id="image_preview" src="{{ asset('management-assets/images/no-image.png') }}" width="120" height="120" style="object-fit: cover;" alt="">
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <input type="file" name="b_image" class="form-control @error('b_image') is-invalid @enderror" onchange="previewImage(this)">
                                        @error('b_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Write your blog content here...',
            tabsize: 2,
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
