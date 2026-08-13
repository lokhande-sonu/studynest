@extends('website.layouts.app')

@section('title', ($blog->b_meta_title ?: $blog->b_title) . ' - StudyNest')
@section('meta_description', $blog->b_meta_description)
@section('meta_keywords', $blog->b_meta_keywords)

@push('styles')
<style>
    .blog-details__text {
        line-height: 1.8;
        font-size: 16px;
    }
    .blog-details__text h2, .blog-details__text h3 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 700;
        color: #1a1a1a;
    }
    .blog-details__text h2 { font-size: 28px; }
    .blog-details__text h3 { font-size: 22px; }
    .blog-details__text ul {
        margin-bottom: 1.5rem;
        padding-left: 1.2rem;
    }
    .blog-details__text ul li {
        margin-bottom: 0.5rem;
        list-style-type: disc;
    }
    .blog-details__text .highlight-box {
        border-left: 4px solid var(--main-600);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .blog-details__text .highlight-box ul li {
        list-style-type: none;
    }
    .blog-details__text img {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
    .final-thoughts-box {
        background-color: var(--main-600);
        border-radius: 12px;
        box-shadow: 0 10px 20px rgba(var(--main-600-rgb), 0.2);
    }
</style>
@endpush

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">{{ $blog->b_title }}</h6>
                <ul class="flex-align gap-8 flex-wrap">
                    <li class="text-sm">
                        <a href="{{ route('website.index') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                            <i class="ph ph-house"></i>
                            Home
                        </a>
                    </li>
                    <li class="flex-align">
                        <i class="ph ph-caret-right"></i>
                    </li>
                    <li class="text-sm">
                        <a href="{{ route('website.blogs') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                            Blogs
                        </a>
                    </li>
                    <li class="flex-align">
                        <i class="ph ph-caret-right"></i>
                    </li>
                    <li class="text-sm text-main-600"> {{ Str::limit($blog->b_title, 30) }} </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <!-- =============================== Blog Details Section Start ======================================== -->
    <section class="blog-details py-80">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-8">
                    <div class="blog-details__content">
                        @if($blog->b_image)
                        <div class="blog-details__thumb mb-32 rounded-16 overflow-hidden">
                            <img src="{{ asset('uploads/blog-photos/' . $blog->b_image) }}" alt="{{ $blog->b_title }}" class="w-100">
                        </div>
                        @endif

                        <div class="flex-align gap-20 mb-24">
                            <span class="text-gray-500 text-sm flex-align gap-8">
                                <i class="ph ph-calendar-blank text-main-600 text-lg"></i> {{ $blog->created_at->format('M d, Y') }}
                            </span>
                            <span class="text-gray-500 text-sm flex-align gap-8">
                                <i class="ph ph-user text-main-600 text-lg"></i> By Admin
                            </span>
                        </div>

                        <h2 class="mb-24">{{ $blog->b_title }}</h2>
                        
                        <div class="blog-details__text text-gray-700">
                            {!! $blog->b_content !!}
                        </div>

                        <div class="mt-40 pt-40 border-top border-gray-100 flex-between flex-wrap gap-16">
                            <div class="flex-align gap-16">
                                <span class="text-gray-900 fw-semibold">Share:</span>
                                <ul class="flex-align gap-12">
                                    <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="w-40 h-40 flex-center bg-main-50 text-main-600 rounded-pill hover-bg-main-600 hover-text-white transition-1"><i class="ph ph-facebook-logo"></i></a></li>
                                    <li><a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $blog->b_title }}" target="_blank" class="w-40 h-40 flex-center bg-main-50 text-main-600 rounded-pill hover-bg-main-600 hover-text-white transition-1"><i class="ph ph-twitter-logo"></i></a></li>
                                    <li><a href="https://api.whatsapp.com/send?text={{ $blog->b_title }} {{ url()->current() }}" target="_blank" class="w-40 h-40 flex-center bg-main-50 text-main-600 rounded-pill hover-bg-main-600 hover-text-white transition-1"><i class="ph ph-whatsapp-logo"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="blog-sidebar border border-gray-100 rounded-16 p-32">
                        <h6 class="mb-24">Recent Posts</h6>
                        <div class="flex-column gap-24 d-flex">
                            @foreach($recentBlogs as $recent)
                            <div class="flex-align gap-16">
                                <a href="{{ route('website.blog-details', $recent->b_slug) }}" class="flex-shrink-0 w-80 h-80 rounded-8 overflow-hidden">
                                    @if($recent->b_image)
                                    <img src="{{ asset('uploads/blog-photos/' . $recent->b_image) }}" alt="" class="w-100 h-100 object-fit-cover">
                                    @else
                                    <img src="{{ asset('management-assets/images/no-image.png') }}" alt="" class="w-100 h-100 object-fit-cover border">
                                    @endif
                                </a>
                                <div class="flex-grow-1">
                                    <h6 class="text-sm mb-8 text-line-2">
                                        <a href="{{ route('website.blog-details', $recent->b_slug) }}" class="hover-text-main-600">{{ $recent->b_title }}</a>
                                    </h6>
                                    <span class="text-gray-400 text-xs">{{ $recent->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-40">
                            <h6 class="mb-24">Tags</h6>
                            <div class="flex-align flex-wrap gap-8">
                                @if($blog->b_meta_keywords)
                                    @foreach(explode(',', $blog->b_meta_keywords) as $keyword)
                                    <span class="py-6 px-16 border border-gray-100 rounded-pill text-sm hover-bg-main-600 hover-text-white hover-border-main-600 transition-1 cursor-pointer">{{ trim($keyword) }}</span>
                                    @endforeach
                                @else
                                    <span class="py-6 px-16 border border-gray-100 rounded-pill text-sm">Education</span>
                                    <span class="py-6 px-16 border border-gray-100 rounded-pill text-sm">School</span>
                                    <span class="py-6 px-16 border border-gray-100 rounded-pill text-sm">Shopping</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =============================== Blog Details Section End ======================================== -->

@endsection
