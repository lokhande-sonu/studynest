
<!-- Advertisement Modal -->
<div class="modal fade" id="adModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-16">
            <div class="modal-header border-0 d-flex align-items-center justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-xl-12">
                        <a href="{{ route('website.prebooking') }}">
                         <img src="{{ asset('customer-web-assets/images/prebooking-hero1.jpeg') }}" alt="School Essentials" class="">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const KEYS = ['sn_ad_shown', 'sn_ad_show'];
    const getCookieMap = () => {
        const map = {};
        (document.cookie || '').split(';').forEach(pair => {
            const p = pair.trim();
            if (!p) return;
            const idx = p.indexOf('=');
            const k = idx >= 0 ? p.slice(0, idx) : p;
            const v = idx >= 0 ? p.slice(idx + 1) : '';
            map[k] = v;
        });
        return map;
    };
    const hasAnyFlag = () => {
        const cookies = getCookieMap();
        return KEYS.some(k => sessionStorage.getItem(k) === '1' || cookies[k] === '1');
    };
    try {
        if (hasAnyFlag()) {
            return;
        }
        var el = document.getElementById('adModal');
        if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = new bootstrap.Modal(el);
            modal.show();
            KEYS.forEach(k => {
                sessionStorage.setItem(k, '1');
                document.cookie = k + '=1; path=/; SameSite=Lax';
            });
        }
    } catch (e) {
        // silent
    }
});
</script>
