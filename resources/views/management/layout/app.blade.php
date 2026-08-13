<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'StudyNest Management Panel')</title>

    <link rel="icon" type="image/png" href="{{ asset('management-assets/images/favicon.png') }}" />

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('management-assets/vendor/bootstrap/css/bootstrap.ltr.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/simplebar/simplebar.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/datatables/css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/css/style.css') }}" />

    <style>
        .sa-sidebar__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-right: 15px;
        }
        .sa-sidebar__close {
            background: none;
            border: none;
            padding: 8px;
            color: #6c757d;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }
        .sa-sidebar__close:hover {
            color: #3d464d;
        }
    </style>

    @yield('styles')
</head>

<body>
<div class="sa-app sa-app--mobile-sidebar-hidden sa-app--toolbar-fixed">

    @include('management.layout.sidebar')

    <div class="sa-app__content">

        @include('management.layout.header')

        <div class="container-fluid px-4 py-3">
            <!--@include('management.layout.flash-messages')-->
            @yield('content')
        </div>

        @include('management.layout.footer')

    </div>
</div>

<script src="{{ asset('management-assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('management-assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('management-assets/vendor/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('management-assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('management-assets/vendor/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('management-assets/vendor/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('management-assets/js/stroyka.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('management-assets/js/custom.js') }}"></script>

<script>
    $(document).on('click', '.editCategoryBtn', function() {
    
        let id = $(this).data('id');
        let name = $(this).data('name');
        let photo = $(this).data('photo');
        let status = $(this).data('status');
    
        let assetBase = "{{ asset(env('PRODUCT_CATEGORY_PHOTO')) }}";
    
        $('#edit_cat_id').val(id);
        $('#edit_cat_name').val(name);
        $('#edit_cat_status').val(status);
    
        $('#editCategoryForm').attr('action',
            "{{ url('management/categories') }}/" + id
        );
    
        $('#edit_cat_photo_preview').attr('src', assetBase + '/' + photo);
    });
    
</script>

@yield('scripts')
</body>
</html>
