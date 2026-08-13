@extends('website.layouts.app')

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">Contact Us</h6>
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
                    <li class="text-sm text-main-600"> Contact Us </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <!-- ============================ Contact Section Start ================================== -->
    <section class="contact py-80">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-8">
                    <div class="contact-box border border-gray-100 bg-white rounded-16 px-24 py-40">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('website.contact-us.store') }}" method="POST">
                            @csrf
                            <h6 class="mb-32">How can we help you?</h6>
                            <div class="row gy-4">
                                <div class="col-sm-6 col-xs-6">
                                    <label for="name"
                                        class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">Full
                                        Name <span class="text-danger text-xl line-height-1">*</span> </label>
                                    <input type="text" class="common-input px-16" id="name" name="name"
                                        placeholder="Full name" required value="{{ old('name') }}">
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <label for="email"
                                        class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">Email
                                        Address <span class="text-danger text-xl line-height-1">*</span> </label>
                                    <input type="email" class="common-input px-16" id="email" name="email"
                                        placeholder="Email address" required value="{{ old('email') }}">
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <label for="phone"
                                        class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">Phone
                                        Number<span class="text-danger text-xl line-height-1">*</span> </label>
                                    <input type="text" class="common-input px-16" id="phone" name="mobile"
                                        placeholder="Phone Number*" required value="{{ old('mobile') }}">
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <label for="subject"
                                        class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">Subject
                                        <span class="text-danger text-xl line-height-1">*</span> </label>
                                    <input type="text" class="common-input px-16" id="subject" name="subject"
                                        placeholder="Subject" required value="{{ old('subject') }}">
                                </div>
                                <div class="col-sm-12">
                                    <label for="message"
                                        class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">Message
                                        <span class="text-danger text-xl line-height-1">*</span> </label>
                                    <textarea class="common-input px-16" id="message" name="message"
                                        placeholder="Type your message" required>{{ old('message') }}</textarea>
                                </div>
                                <div class="col-sm-12 mt-32">
                                    <button type="submit" class="btn btn-main py-18 px-32 rounded-8">Request a
                                        CallBack!</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="contact-box border border-gray-100 bg-white rounded-16 px-24 py-40">
                        <h6 class="mb-48">Get In Touch - Quick Support Options</h6>
                        <div class="flex-align gap-16 mb-16">
                            <span
                                class="w-40 h-40 flex-center rounded-circle border border-gray-100 text-main-two-600 text-2xl flex-shrink-0"><i
                                    class="ph-fill ph-phone-call"></i></span>
                            <a href="tel:{{ $contactInfo->mobile ?? '+917389942021' }}"
                                class="text-md text-gray-900 hover-text-main-600">{{ $contactInfo->mobile ?? '+91 7389942021' }}</a>
                        </div>
                        <div class="flex-align gap-16 mb-16">
                            <span
                                class="w-40 h-40 flex-center rounded-circle border border-gray-100 text-main-two-600 text-2xl flex-shrink-0"><i
                                    class="ph-fill ph-whatsapp-logo"></i></span>
                            <a href="https://wa.me/{{ $contactInfo->whatsapp ?? '917389087022' }}"
                                class="text-md text-gray-900 hover-text-main-600">{{ $contactInfo->whatsapp ?? '+91 73890 87022' }}</a>
                        </div>
                        <div class="flex-align gap-16 mb-16">
                            <span
                                class="w-40 h-40 flex-center rounded-circle border border-gray-100 text-main-two-600 text-2xl flex-shrink-0"><i
                                    class="ph-fill ph-envelope"></i></span>
                            <a href="mailto:{{ $contactInfo->email ?? 'colloborate@studynested.com' }}"
                                class="text-md text-gray-900 hover-text-main-600">{{ $contactInfo->email ??
                                'colloborate@studynested.com' }}</a>
                        </div>
                        <div class="flex-align gap-16 mb-0">
                            <span
                                class="w-40 h-40 flex-center rounded-circle border border-gray-100 text-main-two-600 text-2xl flex-shrink-0"><i
                                    class="ph-fill ph-map-pin"></i></span>
                            <span
                                class="text-md text-gray-900 ">{{ $contactInfo->address ?? 'Bhopal, Madhya Pradesh, India' }}</span>
                        </div>
                    </div>
                    <div class="mt-24 flex-align flex-wrap gap-16">
                        <a href="tel:{{ $contactInfo->mobile ?? '+917389942021' }}"
                            class="bg-neutral-600 hover-bg-main-600 rounded-8 p-10 px-16 flex-between flex-wrap gap-8 flex-grow-1">
                            <span class="text-white fw-medium">Get Support On Call</span>
                            <span class="w-36 h-36 bg-main-600 rounded-8 flex-center text-xl text-white"><i
                                    class="ph ph-headset"></i></span>
                        </a>
                        <a href="mailto:{{ $contactInfo->email ?? 'colloborate@studynested.com' }}"
                            class="bg-neutral-600 hover-bg-main-600 rounded-8 p-10 px-16 flex-between flex-wrap gap-8 flex-grow-1">
                            <span class="text-white fw-medium">Send A Mail</span>
                            <span class="w-36 h-36 bg-main-600 rounded-8 flex-center text-xl text-white"><i
                                    class="ph ph-envelope"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ============================ Contact Section End ================================== -->


@endsection