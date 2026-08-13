<!DOCTYPE html>
<html lang="en" dir="ltr" data-scompiler-id="0">

<head>
    <meta charSet="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="format-detection" content="telephone=no" />
    <title>Forgot Password - Study Nest Management Panel</title><!-- icon -->
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
            <div class="card-body p-sm-5 m-sm-3 flex-grow-0">
                <img src="{{ asset('management-assets/images/logo.png') }}" alt="" width="200">
            <hr/>
                <h1 class="mb-0 fs-3">Forgot Password?</h1>
                <div class="fs-exact-14 text-muted mt-2 pt-1 mb-5 pb-2">Enter the email address associated with your
                    account and we will send a link to recover your password.</div>
                
                 @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <div>{{ session('success') }}</div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    @endif
                                    
                                    @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <div>{{ session('error') }}</div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    @endif
                                    
                                    @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                                        <div class="d-flex">
                                            <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                                            <div>
                                                <ul class="mb-0 ps-3">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    @endif
                
                 <form method="POST" action="{{ route('forgot-password') }}">
                @csrf
                <div class="mb-4"><label class="form-label">Email Address</label>
                    <input class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" type="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus />
                    @error('email')
                                            <div class="invalid-feedback d-block mt-1 small">
                                                {{ $message }}
                                            </div>
                                            @enderror
                </div>
                
                <div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Recover Password</button>
                </div>
                </form>
                
                <div class="form-group mb-0 mt-4 pt-2 text-center text-muted">Remember your password? <a
                        href="{{ route('login') }}">Sign In</a></div>
            </div>
            
            
        </div>
        
    </div><!-- scripts -->
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