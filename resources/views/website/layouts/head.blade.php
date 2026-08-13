<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $meta_title ?? (trim($__env->yieldContent('title')) ?: 'Study Nest - Simplifying School Shopping') }}</title>
@if(!empty($meta_description))
<meta name="description" content="{{ $meta_description }}">
@endif
@if(!empty($meta_keywords))
<meta name="keywords" content="{{ is_array($meta_keywords) ? implode(', ', $meta_keywords) : $meta_keywords }}">
@endif
<!-- Favicon -->
<link rel="shortcut icon" href="{{ asset('customer-web-assets/images/logo/favicon.png') }}">

<!-- Bootstrap -->
<link rel="stylesheet" href="{{ asset('customer-web-assets/css/bootstrap.min.css') }}">
<!-- select 2 -->
<link rel="stylesheet" href="{{ asset('customer-web-assets/css/select2.min.css') }}">
<!-- Slick -->
<link rel="stylesheet" href="{{ asset('customer-web-assets/css/slick.css') }}">
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<!-- Jquery Ui -->
<link rel="stylesheet" href="{{ asset('customer-web-assets/css/jquery-ui.css') }}">
<!-- animate -->
<link rel="stylesheet" href="{{ asset('customer-web-assets/css/animate.css') }}">
<!-- AOS Animation -->
<link rel="stylesheet" href="{{ asset('customer-web-assets/css/aos.css') }}">
<!-- Main css -->
<link rel="stylesheet" href="{{ asset('customer-web-assets/css/main.css') }}">
<!--Google Search Console-->
<meta name="google-site-verification" content="tSGbIM5Qqzt40FBHnxSX9fb-sCJyi-auMaIHyovltfk" />
<!--Google Analytics Tag-->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-S4S37SKE7G"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-S4S37SKE7G');
</script>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '4244023282478865');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=4244023282478865&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

@stack('styles')
