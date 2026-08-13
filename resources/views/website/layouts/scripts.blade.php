<!-- Jquery js -->
<script src="{{ asset('customer-web-assets/js/jquery-3.7.1.min.js') }}"></script>
<!-- Bootstrap Bundle Js -->
<script src="{{ asset('customer-web-assets/js/boostrap.bundle.min.js') }}"></script>
<!-- Phosphor Icon -->
<script src="{{ asset('customer-web-assets/js/phosphor-icon.js') }}"></script>
<!-- Select 2 -->
<script src="{{ asset('customer-web-assets/js/select2.min.js') }}"></script>
<!-- Slick js -->
<script src="{{ asset('customer-web-assets/js/slick.min.js') }}"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<!-- count down js -->
<script src="{{ asset('customer-web-assets/js/count-down.js') }}"></script>
<!-- jquery UI js -->
<script src="{{ asset('customer-web-assets/js/jquery-ui.js') }}"></script>
<!-- wow js -->
<script src="{{ asset('customer-web-assets/js/wow.min.js') }}"></script>
<!-- AOS Animation -->
<script src="{{ asset('customer-web-assets/js/aos.js') }}"></script>
<!-- marque -->
<script src="{{ asset('customer-web-assets/js/marque.min.js') }}"></script>
<!-- vanilla tilt -->
<script src="{{ asset('customer-web-assets/js/vanilla-tilt.min.js') }}"></script>
<!-- Counter -->
<script src="{{ asset('customer-web-assets/js/counter.min.js') }}"></script>
<!-- main js -->
<script src="{{ asset('customer-web-assets/js/main.js') }}"></script>

<script>
// Global toast notifications
window.snToast = function(type, text) {
    const colors = {
        success: '#198754',
        error: '#dc3545',
        warning: '#fd7e14',
        info: '#0d6efd'
    };
    const stackId = 'sn-toast-stack';
    let stack = document.getElementById(stackId);
    if (!stack) {
        stack = document.createElement('div');
        stack.id = stackId;
        stack.style.position = 'fixed';
        stack.style.top = '16px';
        stack.style.right = '16px';
        stack.style.zIndex = '3000';
        stack.style.display = 'flex';
        stack.style.flexDirection = 'column';
        stack.style.gap = '8px';
        document.body.appendChild(stack);
    }
    const toast = document.createElement('div');
    toast.style.minWidth = '220px';
    toast.style.maxWidth = '320px';
    toast.style.padding = '10px 14px';
    toast.style.borderRadius = '8px';
    toast.style.boxShadow = '0 4px 16px rgba(0,0,0,0.12)';
    toast.style.color = '#fff';
    toast.style.background = colors[type] || colors.info;
    toast.style.fontSize = '14px';
    toast.textContent = text;
    stack.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity .3s';
        setTimeout(() => toast.remove(), 300);
    }, 2200);
};
// Backward compat for pages using showMiniToast(msg, isError)
window.showMiniToast = function(msg, isError){ window.snToast(isError ? 'error' : 'success', msg); };
</script>

<script>
    @if(session('open_login'))
        document.addEventListener('DOMContentLoaded', function() {
            var loginModalEl = document.getElementById('loginModal');
            if (loginModalEl && typeof bootstrap !== 'undefined') {
                var loginModal = new bootstrap.Modal(loginModalEl);
                loginModal.show();
            }
        });
    @endif
</script>

<script>
// Convert PHP flash messages to toasts
document.addEventListener('DOMContentLoaded', function(){
    @if(session('success'))
        window.snToast('success', @json(session('success')));
    @endif
    @if(session('error'))
        window.snToast('error', @json(session('error')));
    @endif
    @if(session('warning'))
        window.snToast('warning', @json(session('warning')));
    @endif
    @if(session('info'))
        window.snToast('info', @json(session('info')));
    @endif
});
</script>

@stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('.school-slider-container')) {
        var swiper = new Swiper('.school-slider-container', {
            slidesPerView: 1.5,
            spaceBetween: 10,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '#school-slider-next',
                prevEl: '#school-slider-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 10,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 10,
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetween: 10,
                },
            }
        });
    }
});
</script>
