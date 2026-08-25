<!DOCTYPE html>
<html lang="en" dir="ltr" data-scompiler-id="0">

<head>
    <meta charSet="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="format-detection" content="telephone=no" />
    <title>Forgot Password - StudyNest Management Panel</title><!-- icon -->
    <link rel="icon" type="image/png" href="{{ asset('management-assets/images/favicon.png') }}" /><!-- fonts -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i" />
    <!-- css -->
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/bootstrap/css/bootstrap.ltr.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/highlight.js/styles/github.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/simplebar/simplebar.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/quill/quill.snow.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/air-datepicker/css/datepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/datatables/css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/nouislider/nouislider.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/vendor/fullcalendar/main.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('management-assets/css/style.css') }}" />
</head>

<body>
    
    <div class="min-h-100 p-0 p-sm-6 d-flex align-items-stretch">
        <div class="card w-25x flex-grow-1 flex-sm-grow-0 m-sm-auto">
            <div class="card-body p-sm-5 m-sm-3">
    
                <img src="{{ asset('management-assets/images/logo.png') }}" alt="" width="200">
                <hr/>
    
                <h1 class="mb-0 fs-3">Reset Password</h1>
                <div class="fs-exact-14 text-muted mt-2 mb-4">
                    Please enter your new password.
                </div>
    
                {{-- Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
    
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
    
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
    
                {{-- Reset Password Form --}}
                <form method="POST" action="{{ route('reset-password') }}">
                    @csrf
    
                    {{-- REQUIRED HIDDEN FIELDS --}}
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="token" value="{{ $token }}">
    
                    <div class="mb-4">
                        <label class="form-label">New Password</label>
                        <input type="password"
                               name="password"
                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                               placeholder="Enter new password"
                               required>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
    
                    <div class="mb-4">
                        <label class="form-label">Confirm Password</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control form-control-lg"
                               placeholder="Confirm new password"
                               required>
                    </div>
    
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Reset Password
                    </button>
                </form>
    
                <div class="text-center text-muted mt-4">
                    Copyright {{ date('Y') }} RB Agro Miling Pvt. Ltd.
                    <br>
                    Powered by <a href="https://nextinlabs.com" class="text-danger" target="_blank">NEXTIN LABS</a>
                </div>
    
            </div>
        </div>
    </div>
        
    <!-- scripts -->
    <script src="{{ asset('management-assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/highlight.js/highlight.pack.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/air-datepicker/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/air-datepicker/js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/fontawesome/js/all.min.js') }}" data-auto-replace-svg="" async=""></script>
    <script src="{{ asset('management-assets/vendor/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ asset('management-assets/vendor/fullcalendar/main.min.js') }}"></script>
    <script src="{{ asset('management-assets/js/stroyka.js') }}"></script>
    <script src="{{ asset('management-assets/js/custom.js') }}"></script>
    <script src="{{ asset('management-assets/js/calendar.js') }}"></script>
    <script src="{{ asset('management-assets/js/demo.js') }}"></script>
    <script src="{{ asset('management-assets/js/demo-chart-js.js') }}"></script>
</body>


</html>