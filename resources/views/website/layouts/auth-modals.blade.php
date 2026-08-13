
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-16">
            <div class="modal-body">
                <div class="row g-4">

                    <!-- Login Card Start -->
                    <div class="col-xl-12">
                        <div
                            class="border border-gray-100 hover-border-main-600 transition-1 rounded-16 px-24 py-40 h-100">
                            <h6 class="text-xl mb-32">Welcome Back! Login to Get Started</h6>

                            <div id="login-errors" class="alert alert-danger d-none"></div>
                            <div id="login-success" class="alert alert-success d-none"></div>

                            <form action="{{ route('website.login') }}" method="POST" id="loginForm">
                                @csrf
                                <div class="mb-24">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium">
                                        Email Address <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" class="common-input"
                                        placeholder="Enter Email Address" required>
                                </div>

                                <div class="mb-4">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium">Password <span
                                            class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" name="password" class="common-input" placeholder="Enter Password" required>
                                        <span
                                            class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y cursor-pointer ph ph-eye-slash"></span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="#" class="text-main-600 text-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                                        Forgot your password?
                                    </a>
                                </div>

                                <div class="">
                                    <div class="d-flex align-items-center gap-4 flex-wrap">
                                        <button type="submit" class="btn btn-main py-18 px-40">Login</button>
                                    </div>
                                </div>
                                <div class="my-10">
                                    <p class="text-gray-500">
                                        If you don't have an account,
                                        <a href="#" class="text-main-600 text-decoration-underline"
                                            data-bs-toggle="modal" data-bs-target="#registerModal">Register Now</a>.
                                    </p>
                                </div>
                            </form>

                        </div>
                    </div>
                    <!-- Login Card End -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Login Form ---
        const loginForm = document.getElementById('loginForm');
        const loginErrors = document.getElementById('login-errors');
        const loginSuccess = document.getElementById('login-success');

        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Clear previous messages
                loginErrors.classList.add('d-none');
                loginErrors.innerHTML = '';
                loginSuccess.classList.add('d-none');
                loginSuccess.innerHTML = '';

                const formData = new FormData(loginForm);
                const csrfToken = loginForm.querySelector('input[name="_token"]').value;

                fetch(loginForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        loginSuccess.textContent = data.message;
                        loginSuccess.classList.remove('d-none');
                        // Redirect
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            location.reload();
                        }
                    } else {
                        loginErrors.textContent = data.message;
                        loginErrors.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loginErrors.textContent = 'An unexpected error occurred. Please try again.';
                    loginErrors.classList.remove('d-none');
                });
            });
        }

        // --- Register Form ---
        const registerForm = document.getElementById('registerForm');
        const registerErrors = document.getElementById('register-errors');
        const registerSuccess = document.getElementById('register-success');

        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear previous messages
                registerErrors.classList.add('d-none');
                registerErrors.innerHTML = '';
                registerSuccess.classList.add('d-none');
                registerSuccess.innerHTML = '';

                const formData = new FormData(registerForm);
                const csrfToken = registerForm.querySelector('input[name="_token"]').value;

                fetch(registerForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        registerSuccess.textContent = data.message;
                        registerSuccess.classList.remove('d-none');
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            location.reload();
                        }
                    } else {
                        let errorMessage = data.message;
                        if (data.errors) {
                            errorMessage += '<ul>';
                            for (const [key, messages] of Object.entries(data.errors)) {
                                messages.forEach(msg => {
                                    errorMessage += `<li>${msg}</li>`;
                                });
                            }
                            errorMessage += '</ul>';
                        }
                        registerErrors.innerHTML = errorMessage;
                        registerErrors.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    registerErrors.textContent = 'An unexpected error occurred. Please try again.';
                    registerErrors.classList.remove('d-none');
                });
            });
        }
        
        document.querySelectorAll('.toggle-password').forEach(function(el){
            el.addEventListener('click', function(){
                var wrapper = el.parentElement;
                if (!wrapper) return;
                var input = wrapper.querySelector('input');
                if (!input) return;
                if (input.type === 'password') {
                    input.type = 'text';
                    el.classList.remove('ph-eye-slash');
                    el.classList.add('ph-eye');
                } else {
                    input.type = 'password';
                    el.classList.remove('ph-eye');
                    el.classList.add('ph-eye-slash');
                }
            });
        });
    });
</script>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-16">
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-xl-12">
                        <div class="border border-gray-100 hover-border-main-600 transition-1 rounded-16 px-24 py-40">
                            <h6 class="text-xl mb-32">Welcome to StudyNest! Register to Start Shopping</h6>

                            <!-- Step 1: Basic Info -->
                            <div id="registerStep1">
                                <div id="register-errors" class="alert alert-danger d-none"></div>
                                <div id="register-success" class="alert alert-success d-none"></div>

                                <form id="registerFormStep1">
                                    @csrf
                                    <!-- Full Name -->
                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="reg-name" class="common-input" placeholder="Enter your Full Name" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">Email address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="reg-email" class="common-input" placeholder="Enter Email Address" required>
                                    </div>

                                    <!-- Password -->
                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">Password <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" name="password" id="reg-password" class="common-input" placeholder="Enter Password" required minlength="6">
                                            <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y cursor-pointer ph ph-eye-slash"></span>
                                        </div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" id="reg-password-confirm" class="common-input" placeholder="Confirm Password" required minlength="6">
                                    </div>

                                    <!-- Mobile -->
                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="tel" name="mobile" id="reg-mobile" class="common-input" placeholder="Enter 10-digit Mobile Number" required pattern="[0-9]{10}" maxlength="10">
                                    </div>

                                    <!-- Privacy -->
                                    <div class="my-10">
                                        <p class="text-gray-500">
                                            By registering, you agree to our
                                            <a href="{{ route('website.privacy-policy') }}" class="text-main-600 text-decoration-underline">Privacy Policy</a> &
                                            <a href="{{ route('website.terms-conditions') }}" class="text-main-600 text-decoration-underline">Terms</a>.
                                        </p>
                                    </div>

                                    <button type="button" id="btn-send-otp" class="btn btn-main py-18 px-40 w-100">
                                        Continue
                                    </button>
                                </form>
                            </div>

                            <!-- Step 2: OTP Verification -->
                            <div id="registerStep2" class="d-none">
                                <div class="text-center mb-24">
                                    <div class="w-64 h-64 bg-main-50 rounded-circle flex-center mx-auto mb-16">
                                        <i class="ph ph-envelope text-3xl text-main-600"></i>
                                    </div>
                                    <h6 class="text-lg mb-8">Verify your email</h6>
                                    <p class="text-gray-500 text-sm">We've sent a 6-digit OTP to <span id="otp-email-display" class="fw-medium text-neutral-900"></span></p>
                                </div>

                                <div id="otp-errors" class="alert alert-danger d-none"></div>
                                <div id="otp-success" class="alert alert-success d-none"></div>

                                <form id="registerFormStep2">
                                    @csrf
                                    <input type="hidden" name="name" id="otp-name">
                                    <input type="hidden" name="email" id="otp-email">
                                    <input type="hidden" name="password" id="otp-password">

                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">Enter OTP</label>
                                        <input type="text" name="otp" id="reg-otp" class="common-input text-center text-xl" maxlength="6" placeholder="000000" required>
                                    </div>

                                    <button type="submit" class="btn btn-main py-18 px-40 w-100 mb-16">
                                        Verify & Register
                                    </button>

                                    <div class="text-center">
                                        <button type="button" id="btn-resend-otp" class="btn btn-link text-main-600 fw-medium p-0">
                                            Resend OTP
                                        </button>
                                        <span id="resend-timer" class="text-gray-500 text-sm ms-8 d-none">(60s)</span>
                                    </div>

                                    <hr class="my-16">

                                    <button type="button" id="btn-back-step1" class="btn btn-outline-main py-12 px-24 w-100">
                                        <i class="ph ph-arrow-left me-8"></i> Back
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-16">
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-xl-12">
                        <div class="border border-gray-100 hover-border-main-600 transition-1 rounded-16 px-24 py-40">
                            <h6 class="text-xl mb-32">Reset Your Password</h6>

                            <!-- Step 1: Enter Email -->
                            <div id="forgotStep1">
                                <div id="forgot-errors" class="alert alert-danger d-none"></div>
                                <div id="forgot-success" class="alert alert-success d-none"></div>

                                <form id="forgotFormStep1">
                                    @csrf
                                    <p class="text-gray-500 mb-24">Enter your email address and we'll send you an OTP to reset your password.</p>

                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="forgot-email" class="common-input" placeholder="Enter your registered email" required>
                                    </div>

                                    <button type="button" id="btn-send-forgot-otp" class="btn btn-main py-18 px-40 w-100">
                                        Send OTP
                                    </button>

                                    <div class="text-center mt-16">
                                        <button type="button" class="btn btn-link text-main-600 fw-medium p-0" data-bs-toggle="modal" data-bs-target="#loginModal">
                                            <i class="ph ph-arrow-left me-8"></i> Back to Login
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Step 2: Verify OTP & Set New Password -->
                            <div id="forgotStep2" class="d-none">
                                <div class="text-center mb-24">
                                    <div class="w-64 h-64 bg-main-50 rounded-circle flex-center mx-auto mb-16">
                                        <i class="ph ph-lock-key text-3xl text-main-600"></i>
                                    </div>
                                    <h6 class="text-lg mb-8">Verify & Set New Password</h6>
                                    <p class="text-gray-500 text-sm">We've sent a 6-digit OTP to <span id="forgot-otp-email-display" class="fw-medium text-neutral-900"></span></p>
                                </div>

                                <div id="forgot-otp-errors" class="alert alert-danger d-none"></div>
                                <div id="forgot-otp-success" class="alert alert-success d-none"></div>

                                <form id="forgotFormStep2">
                                    @csrf
                                    <input type="hidden" name="email" id="forgot-otp-email">

                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">Enter OTP</label>
                                        <input type="text" name="otp" id="forgot-otp" class="common-input text-center text-xl" maxlength="6" placeholder="000000" required>
                                    </div>

                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">New Password <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" name="password" id="forgot-new-password" class="common-input" placeholder="Enter new password" required minlength="6">
                                            <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y cursor-pointer ph ph-eye-slash"></span>
                                        </div>
                                    </div>

                                    <div class="mb-24">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium">Confirm New Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" id="forgot-confirm-password" class="common-input" placeholder="Confirm new password" required minlength="6">
                                    </div>

                                    <button type="submit" class="btn btn-main py-18 px-40 w-100 mb-16">
                                        Reset Password
                                    </button>

                                    <div class="text-center">
                                        <button type="button" id="btn-resend-forgot-otp" class="btn btn-link text-main-600 fw-medium p-0">
                                            Resend OTP
                                        </button>
                                        <span id="forgot-resend-timer" class="text-gray-500 text-sm ms-8 d-none">(60s)</span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Step 1 Elements
    const step1 = document.getElementById('registerStep1');
    const step2 = document.getElementById('registerStep2');
    const btnSendOtp = document.getElementById('btn-send-otp');
    const btnBackStep1 = document.getElementById('btn-back-step1');
    const btnResendOtp = document.getElementById('btn-resend-otp');
    const resendTimer = document.getElementById('resend-timer');

    const regName = document.getElementById('reg-name');
    const regEmail = document.getElementById('reg-email');
    const regPassword = document.getElementById('reg-password');
    const regPasswordConfirm = document.getElementById('reg-password-confirm');
    const regMobile = document.getElementById('reg-mobile');

    const otpName = document.getElementById('otp-name');
    const otpEmail = document.getElementById('otp-email');
    const otpPassword = document.getElementById('otp-password');
    const otpEmailDisplay = document.getElementById('otp-email-display');
    const regOtp = document.getElementById('reg-otp');

    const registerErrors = document.getElementById('register-errors');
    const registerSuccess = document.getElementById('register-success');
    const otpErrors = document.getElementById('otp-errors');
    const otpSuccess = document.getElementById('otp-success');

    let resendTimeout = null;
    let resendSeconds = 60;

    // Direct Registration (OTP Bypass)
    btnSendOtp.addEventListener('click', function() {
        registerErrors.classList.add('d-none');
        registerSuccess.classList.add('d-none');

        // Validation
        if (!regName.value || !regEmail.value || !regPassword.value || !regPasswordConfirm.value || !regMobile.value) {
            registerErrors.textContent = 'All fields are required';
            registerErrors.classList.remove('d-none');
            return;
        }

        if (regPassword.value !== regPasswordConfirm.value) {
            registerErrors.textContent = 'Passwords do not match';
            registerErrors.classList.remove('d-none');
            return;
        }

        if (regPassword.value.length < 6) {
            registerErrors.textContent = 'Password must be at least 6 characters';
            registerErrors.classList.remove('d-none');
            return;
        }

        btnSendOtp.disabled = true;
        btnSendOtp.innerHTML = '<span class="spinner-border spinner-border-sm me-8"></span>Registering...';

        if (!/^[0-9]{10}$/.test(regMobile.value)) {
            registerErrors.textContent = 'Please enter a valid 10-digit mobile number';
            registerErrors.classList.remove('d-none');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('name', regName.value);
        formData.append('email', regEmail.value);
        formData.append('password', regPassword.value);
        formData.append('mobile', regMobile.value);

        // Direct registration without OTP
        fetch('{{ route("website.register") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                registerSuccess.textContent = data.message || 'Registration successful!';
                registerSuccess.classList.remove('d-none');

                // Redirect or reload after 1 second
                setTimeout(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        location.reload();
                    }
                }, 1000);
            } else {
                registerErrors.textContent = data.message || 'Registration failed';
                registerErrors.classList.remove('d-none');
            }
        })
        .catch(() => {
            registerErrors.textContent = 'Network error. Please try again.';
            registerErrors.classList.remove('d-none');
        })
        .finally(() => {
            btnSendOtp.disabled = false;
            btnSendOtp.innerHTML = 'Continue';
        });
    });

    // Back to step 1
    btnBackStep1.addEventListener('click', function() {
        step2.classList.add('d-none');
        step1.classList.remove('d-none');
        otpErrors.classList.add('d-none');
        otpSuccess.classList.add('d-none');
        clearTimeout(resendTimeout);
        resendTimer.classList.add('d-none');
        btnResendOtp.disabled = false;
        btnResendOtp.textContent = 'Resend OTP';
    });

    // Resend OTP
    btnResendOtp.addEventListener('click', function() {
        btnResendOtp.disabled = true;
        otpErrors.classList.add('d-none');

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('name', otpName.value);
        formData.append('email', otpEmail.value);
        formData.append('password', otpPassword.value);

        fetch('{{ route("website.register.send-otp") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                otpSuccess.textContent = 'OTP sent successfully!';
                otpSuccess.classList.remove('d-none');
                startResendTimer();
            } else {
                otpErrors.textContent = data.message || 'Failed to resend OTP';
                otpErrors.classList.remove('d-none');
                btnResendOtp.disabled = false;
            }
        })
        .catch(() => {
            otpErrors.textContent = 'Network error. Please try again.';
            otpErrors.classList.remove('d-none');
            btnResendOtp.disabled = false;
        });
    });

    // Start resend timer
    function startResendTimer() {
        resendSeconds = 60;
        btnResendOtp.disabled = true;
        resendTimer.classList.remove('d-none');
        updateResendTimer();
    }

    function updateResendTimer() {
        if (resendSeconds > 0) {
            resendTimer.textContent = '(' + resendSeconds + 's)';
            resendSeconds--;
            resendTimeout = setTimeout(updateResendTimer, 1000);
        } else {
            resendTimer.classList.add('d-none');
            btnResendOtp.disabled = false;
        }
    }

    // Verify OTP and Register
    document.getElementById('registerFormStep2').addEventListener('submit', function(e) {
        e.preventDefault();
        otpErrors.classList.add('d-none');
        otpSuccess.classList.add('d-none');

        if (!regOtp.value || regOtp.value.length !== 6) {
            otpErrors.textContent = 'Please enter a valid 6-digit OTP';
            otpErrors.classList.remove('d-none');
            return;
        }

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-8"></span>Verifying...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('name', otpName.value);
        formData.append('email', otpEmail.value);
        formData.append('password', otpPassword.value);
        formData.append('otp', regOtp.value);

        fetch('{{ route("website.register") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                otpSuccess.textContent = 'Registration successful! Redirecting...';
                otpSuccess.classList.remove('d-none');
                window.location.reload();
            } else {
                otpErrors.textContent = data.message || 'Invalid OTP';
                otpErrors.classList.remove('d-none');
            }
        })
        .catch(() => {
            otpErrors.textContent = 'Network error. Please try again.';
            otpErrors.classList.remove('d-none');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Verify & Register';
        });
    });

    // ==================== FORGOT PASSWORD ====================
    const forgotStep1 = document.getElementById('forgotStep1');
    const forgotStep2 = document.getElementById('forgotStep2');
    const btnSendForgotOtp = document.getElementById('btn-send-forgot-otp');
    const btnResendForgotOtp = document.getElementById('btn-resend-forgot-otp');
    const forgotResendTimer = document.getElementById('forgot-resend-timer');

    const forgotEmail = document.getElementById('forgot-email');
    const forgotOtpEmail = document.getElementById('forgot-otp-email');
    const forgotOtpEmailDisplay = document.getElementById('forgot-otp-email-display');
    const forgotOtp = document.getElementById('forgot-otp');
    const forgotNewPassword = document.getElementById('forgot-new-password');
    const forgotConfirmPassword = document.getElementById('forgot-confirm-password');

    const forgotErrors = document.getElementById('forgot-errors');
    const forgotSuccess = document.getElementById('forgot-success');
    const forgotOtpErrors = document.getElementById('forgot-otp-errors');
    const forgotOtpSuccess = document.getElementById('forgot-otp-success');

    let forgotResendTimeout = null;
    let forgotResendSeconds = 60;

    // Send Forgot Password OTP
    if (btnSendForgotOtp) {
        btnSendForgotOtp.addEventListener('click', function() {
            forgotErrors.classList.add('d-none');
            forgotSuccess.classList.add('d-none');

            if (!forgotEmail.value) {
                forgotErrors.textContent = 'Please enter your email address';
                forgotErrors.classList.remove('d-none');
                return;
            }

            btnSendForgotOtp.disabled = true;
            btnSendForgotOtp.innerHTML = '<span class="spinner-border spinner-border-sm me-8"></span>Sending...';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('email', forgotEmail.value);

            fetch('{{ route("website.forgot-password.send-otp") }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    forgotOtpEmail.value = forgotEmail.value;
                    forgotOtpEmailDisplay.textContent = forgotEmail.value;
                    forgotStep1.classList.add('d-none');
                    forgotStep2.classList.remove('d-none');
                    forgotSuccess.classList.add('d-none');
                    startForgotResendTimer();
                } else {
                    forgotErrors.textContent = data.message || 'Failed to send OTP';
                    forgotErrors.classList.remove('d-none');
                }
            })
            .catch(() => {
                forgotErrors.textContent = 'Network error. Please try again.';
                forgotErrors.classList.remove('d-none');
            })
            .finally(() => {
                btnSendForgotOtp.disabled = false;
                btnSendForgotOtp.innerHTML = 'Send OTP';
            });
        });
    }

    // Resend Forgot Password OTP
    if (btnResendForgotOtp) {
        btnResendForgotOtp.addEventListener('click', function() {
            if (btnResendForgotOtp.disabled) return;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('email', forgotOtpEmail.value);

            btnResendForgotOtp.disabled = true;

            fetch('{{ route("website.forgot-password.send-otp") }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    startForgotResendTimer();
                    forgotOtpSuccess.textContent = 'OTP sent successfully!';
                    forgotOtpSuccess.classList.remove('d-none');
                } else {
                    forgotOtpErrors.textContent = data.message || 'Failed to resend OTP';
                    forgotOtpErrors.classList.remove('d-none');
                }
            })
            .catch(() => {
                forgotOtpErrors.textContent = 'Network error. Please try again.';
                forgotOtpErrors.classList.remove('d-none');
            })
            .finally(() => {
                btnResendForgotOtp.disabled = false;
            });
        });
    }

    function startForgotResendTimer() {
        forgotResendSeconds = 60;
        btnResendForgotOtp.disabled = true;
        forgotResendTimer.classList.remove('d-none');
        updateForgotResendTimer();
    }

    function updateForgotResendTimer() {
        if (forgotResendSeconds > 0) {
            forgotResendTimer.textContent = '(' + forgotResendSeconds + 's)';
            forgotResendSeconds--;
            forgotResendTimeout = setTimeout(updateForgotResendTimer, 1000);
        } else {
            forgotResendTimer.classList.add('d-none');
            btnResendForgotOtp.disabled = false;
        }
    }

    // Reset Password Form Submit
    const forgotFormStep2 = document.getElementById('forgotFormStep2');
    if (forgotFormStep2) {
        forgotFormStep2.addEventListener('submit', function(e) {
            e.preventDefault();
            forgotOtpErrors.classList.add('d-none');
            forgotOtpSuccess.classList.add('d-none');

            if (!forgotOtp.value || forgotOtp.value.length !== 6) {
                forgotOtpErrors.textContent = 'Please enter a valid 6-digit OTP';
                forgotOtpErrors.classList.remove('d-none');
                return;
            }

            if (!forgotNewPassword.value || forgotNewPassword.value.length < 6) {
                forgotOtpErrors.textContent = 'Password must be at least 6 characters';
                forgotOtpErrors.classList.remove('d-none');
                return;
            }

            if (forgotNewPassword.value !== forgotConfirmPassword.value) {
                forgotOtpErrors.textContent = 'Passwords do not match';
                forgotOtpErrors.classList.remove('d-none');
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-8"></span>Resetting...';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('email', forgotOtpEmail.value);
            formData.append('otp', forgotOtp.value);
            formData.append('password', forgotNewPassword.value);
            formData.append('password_confirmation', forgotConfirmPassword.value);

            fetch('{{ route("website.forgot-password.reset") }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    forgotOtpSuccess.textContent = 'Password reset successful! Redirecting to login...';
                    forgotOtpSuccess.classList.remove('d-none');
                    setTimeout(() => {
                        const forgotModal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                        forgotModal.hide();
                        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                        loginModal.show();
                        // Reset form
                        forgotStep2.classList.add('d-none');
                        forgotStep1.classList.remove('d-none');
                        forgotFormStep2.reset();
                        document.getElementById('forgotFormStep1').reset();
                    }, 2000);
                } else {
                    forgotOtpErrors.textContent = data.message || 'Failed to reset password';
                    forgotOtpErrors.classList.remove('d-none');
                }
            })
            .catch(() => {
                forgotOtpErrors.textContent = 'Network error. Please try again.';
                forgotOtpErrors.classList.remove('d-none');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Reset Password';
            });
        });
    }
});
</script>
