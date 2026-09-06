<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\WebsiteController; 
use App\Http\Controllers\WebsiteAuthController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth Routes
Route::get('/management/login', [AuthController::class, 'showLogin'])->name('show-login');
Route::post('/management/login', [AuthController::class, 'login'])->name('login');
Route::get('/management/logout', [AuthController::class, 'logout'])->name('management.logout');

Route::get('/management/forgot-password', [AuthController::class, 'showForgotPassword'])->name('show-forgot-password');
Route::post('/management/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
Route::get('/management/reset-password', [AuthController::class, 'showResetPassword'])->name('show-reset-password');
Route::post('/management/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

// Management Routes
Route::prefix('management')->middleware(['auth:management', 'disableBackBtn'])->group(function () {
    Route::get('/dashboard', [ManagementController::class, 'dashboard'])->name('management.dashboard');
    
    // Resource Routes
    Route::resource('schools', \App\Http\Controllers\Management\SchoolController::class, ['as' => 'management']);
    Route::get('schools/export/excel', [\App\Http\Controllers\Management\SchoolController::class, 'exportExcel'])->name('management.schools.export');
    Route::post('schools/import/excel', [\App\Http\Controllers\Management\SchoolController::class, 'importExcel'])->name('management.schools.import');

    Route::resource('classes', \App\Http\Controllers\Management\ClassController::class, ['as' => 'management']);
    Route::get('classes/export/excel', [\App\Http\Controllers\Management\ClassController::class, 'exportExcel'])->name('management.classes.export');
    Route::post('classes/import/excel', [\App\Http\Controllers\Management\ClassController::class, 'importExcel'])->name('management.classes.import');

    Route::resource('subjects', \App\Http\Controllers\Management\SubjectController::class, ['as' => 'management']);
    Route::get('subjects/export/excel', [\App\Http\Controllers\Management\SubjectController::class, 'exportExcel'])->name('management.subjects.export');
    Route::post('subjects/import/excel', [\App\Http\Controllers\Management\SubjectController::class, 'importExcel'])->name('management.subjects.import');

    Route::resource('products', \App\Http\Controllers\Management\ProductController::class, ['as' => 'management']);
    Route::get('products-export', [\App\Http\Controllers\Management\ProductController::class, 'exportExcel'])->name('management.products.export');
    Route::post('products-import', [\App\Http\Controllers\Management\ProductController::class, 'importExcel'])->name('management.products.import');
    Route::resource('bundles', \App\Http\Controllers\Management\BundleController::class, ['as' => 'management']);
    Route::get('bundles/{id}/products', [\App\Http\Controllers\Management\BundleController::class, 'getProducts'])->name('management.bundles.products');
    Route::post('bundles/{id}/products', [\App\Http\Controllers\Management\BundleController::class, 'updateProducts'])->name('management.bundles.update-products');
    Route::resource('categories', \App\Http\Controllers\Management\ProductCategoryController::class, ['as' => 'management']);
    Route::put('categories/{id}/update-status', [\App\Http\Controllers\Management\ProductCategoryController::class, 'updateStatus'])->name('management.categories.update-status');
    Route::resource('customers', \App\Http\Controllers\Management\CustomerController::class, ['as' => 'management']);
    Route::put('customers/{id}/update-status', [\App\Http\Controllers\Management\CustomerController::class, 'updateStatus'])->name('management.customers.update-status');
    Route::resource('app-slider', \App\Http\Controllers\Management\AppSliderController::class, ['as' => 'management']);
    Route::put('app-slider/{id}/update-status', [\App\Http\Controllers\Management\AppSliderController::class, 'updateStatus'])->name('management.app-slider.update-status');
    
    // Blog Management
    Route::resource('blogs', \App\Http\Controllers\Management\BlogController::class, ['as' => 'management']);
    Route::put('blogs/{id}/update-status', [\App\Http\Controllers\Management\BlogController::class, 'updateStatus'])->name('management.blogs.update-status');

    // Contact Info (Singleton)
    Route::get('contact-info', [\App\Http\Controllers\Management\ContactInfoController::class, 'index'])->name('management.contact-info.index');
    Route::put('contact-info', [\App\Http\Controllers\Management\ContactInfoController::class, 'update'])->name('management.contact-info.update');

    // Admin Profile
    Route::get('/profile', [\App\Http\Controllers\Management\AdminProfileController::class, 'index'])->name('management.profile.index');
    Route::post('/profile', [\App\Http\Controllers\Management\AdminProfileController::class, 'update'])->name('management.profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Management\AdminProfileController::class, 'updatePassword'])->name('management.profile.password');

    // Orders Section
    Route::get('/order', [\App\Http\Controllers\Management\OrderController::class, 'index'])->name('management.order.index');
    Route::get('/order/details/{id}', [\App\Http\Controllers\Management\OrderController::class, 'show'])->name('management.order.details');
    Route::put('/order/{id}', [\App\Http\Controllers\Management\OrderController::class, 'update'])->name('management.order.update');
    Route::put('/order/{id}/update-status', [\App\Http\Controllers\Management\OrderController::class, 'updateStatus'])->name('management.order.update-status');
    Route::put('/order/{id}/update-payment-status', [\App\Http\Controllers\Management\OrderController::class, 'updatePaymentStatus'])->name('management.order.update-payment-status');

    Route::get('/order/{id}/generate-invoice', [\App\Http\Controllers\Management\OrderController::class, 'generateInvoice'])->name('management.order.generate-invoice');

    // Pre-Booking
    Route::get('/prebookings', [\App\Http\Controllers\Management\PreBookingController::class, 'index'])->name('management.prebooking.index');
    Route::get('/prebookings/export', [\App\Http\Controllers\Management\PreBookingController::class, 'exportExcel'])->name('management.prebooking.export');

    Route::post('/prebookings/{id}/status', [\App\Http\Controllers\Management\PreBookingController::class, 'updateStatus'])->name('management.prebooking.status');

    // Orders Report Section
    Route::get('/order-report', [\App\Http\Controllers\Management\OrderController::class, 'OrderReportindex'])->name('management.order-report.index');
    Route::get('/order-report/export', [\App\Http\Controllers\Management\OrderController::class, 'exportOrderReport'])->name('management.order-report.export');
    Route::get('/order-report/export/pdf', [\App\Http\Controllers\Management\OrderController::class, 'exportPdf'])->name('management.order-report.export-pdf');

    // Transaction Report Section
    Route::get('/transaction-report', [\App\Http\Controllers\Management\OrderController::class, 'transactionReport'])->name('management.transaction-report.index');
    Route::get('/transaction-report/export', [\App\Http\Controllers\Management\OrderController::class, 'exportTransactionReportExcel'])->name('management.transaction-report.export');
    Route::get('/transaction-report/export-pdf', [\App\Http\Controllers\Management\OrderController::class, 'exportTransactionReportPdf'])->name('management.transaction-report.export-pdf');
    
    // Customer Report Section
    Route::get('/customer-report', [\App\Http\Controllers\Management\CustomerController::class, 'customerReport'])->name('management.customer-report.index');
    Route::get('/customer-report/export', [\App\Http\Controllers\Management\CustomerController::class, 'exportCustomerReportExcel'])->name('management.customer-report.export');
    Route::get('/customer-report/export-pdf', [\App\Http\Controllers\Management\CustomerController::class, 'exportCustomerReportPdf'])->name('management.customer-report.export-pdf');
    
    // Charges Section
    Route::get('/charges', [\App\Http\Controllers\Management\ChargesController::class, 'index'])->name('management.charges.index');
    Route::post('/charges', [\App\Http\Controllers\Management\ChargesController::class, 'store'])->name('management.charges.store');
    Route::put('/charges/{id}', [\App\Http\Controllers\Management\ChargesController::class, 'update'])->name('management.charges.update');
    Route::put('/charges/{id}/update-status', [\App\Http\Controllers\Management\ChargesController::class, 'updateStatus'])->name('management.charges.update-status');
    Route::delete('/charges/{id}', [\App\Http\Controllers\Management\ChargesController::class, 'destroy'])->name('management.charges.destroy');
});

//Website Routes
Route::post('/login', [WebsiteAuthController::class, 'login'])->name('website.login');
Route::post('/register/send-otp', [WebsiteAuthController::class, 'sendOtp'])->name('website.register.send-otp');
Route::post('/register', [WebsiteAuthController::class, 'register'])->name('website.register');
Route::post('/forgot-password/send-otp', [WebsiteAuthController::class, 'sendForgotPasswordOtp'])->name('website.forgot-password.send-otp');
Route::post('/forgot-password/reset', [WebsiteAuthController::class, 'resetPasswordWithOtp'])->name('website.forgot-password.reset');
Route::get('/logout', [WebsiteAuthController::class, 'logout'])->name('website.logout');

Route::get('/', [WebsiteController::class, 'index'])->name('website.index');
Route::get('/prebooking', [WebsiteController::class, 'prebooking'])->name('website.prebooking');
Route::post('/prebooking', [WebsiteController::class, 'prebookingStore'])->name('website.prebooking.store');
Route::get('/about-us', [WebsiteController::class, 'about'])->name('website.about-us');
Route::get('/contact-us', [WebsiteController::class, 'contact'])->name('website.contact-us');
Route::post('/contact-us', [WebsiteController::class, 'contactStore'])->name('website.contact-us.store');
Route::get('/product-details/{id}', [WebsiteController::class, 'productDetails'])->name('website.product-details');

// Blog Routes
Route::get('/blogs', [WebsiteController::class, 'blogs'])->name('website.blogs');
Route::get('/blog/{slug}', [WebsiteController::class, 'blogDetails'])->name('website.blog-details');

Route::middleware(['disableBackBtn'])->group(function () {
    Route::get('/cart', [WebsiteController::class, 'cart'])->name('website.cart');
    Route::post('/cart/add/{id}', [WebsiteController::class, 'addToCart'])->name('website.cart.add');
    Route::post('/cart/bulk-add', [WebsiteController::class, 'bulkAddToCart'])->name('website.cart.bulk-add');
    Route::patch('/cart/update', [WebsiteController::class, 'updateCart'])->name('website.cart.update');
    Route::delete('/cart/remove', [WebsiteController::class, 'removeFromCart'])->name('website.cart.remove');
    Route::get('/checkout', [WebsiteController::class, 'checkout'])->name('website.checkout');
    Route::post('/checkout/place-order', [WebsiteController::class, 'placeOrder'])->name('website.checkout.place-order');
    Route::get('/thank-you', [WebsiteController::class, 'thankYou'])->name('website.thank-you');

    // PayU Payment Gateway
    Route::post('/payu/callback', [WebsiteController::class, 'payuCallback'])->name('website.payu.callback');
    Route::post('/payu/webhook', [WebsiteController::class, 'payuWebhook'])->name('website.payu.webhook');

    // My Orders Routes
    Route::get('/my-orders', [WebsiteController::class, 'myOrders'])->name('website.orders.index');
    Route::get('/my-orders/{id}', [WebsiteController::class, 'myOrderDetails'])->name('website.orders.show');
    Route::get('/my-orders/{id}/invoice', [WebsiteController::class, 'orderInvoice'])->name('website.orders.invoice');
    Route::post('/my-orders/{id}/cancel', [WebsiteController::class, 'cancelOrder'])->name('website.orders.cancel');
});

Route::get('/shop', [WebsiteController::class, 'shop'])->name('website.shop');
Route::get('/school-shop', [WebsiteController::class, 'Schoolshop'])->name('website.school-shop');
Route::get('/schools', [WebsiteController::class, 'schools'])->name('website.schools');
Route::get('/bundles', [WebsiteController::class, 'bundles'])->name('website.bundles');
Route::get('/bundles/{id}', [WebsiteController::class, 'bundleDetails'])->name('website.bundle-details');

Route::get('/terms-conditions', [WebsiteController::class, 'termsConditions'])->name('website.terms-conditions');
Route::get('/privacy-policy', [WebsiteController::class, 'privacyPolicy'])->name('website.privacy-policy');
Route::get('/refund-policy', [WebsiteController::class, 'refundPolicy'])->name('website.refund-policy');
Route::get('/cancellation-policy', [WebsiteController::class, 'cancellationPolicy'])->name('website.cancellation-policy');
Route::get('/disclaimer', [WebsiteController::class, 'disclaimer'])->name('website.disclaimer');
Route::post('/order/track', [WebsiteController::class, 'trackOrder'])->name('website.order.track');
Route::get('/search/suggestions', [WebsiteController::class, 'searchSuggestions'])->name('website.search.suggestions');
Route::get('/delete-account-request', [WebsiteController::class, 'deleteAccountRequest'])->name('website.delete-account-request');
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Meta Product Catalog Feed
Route::get('/feed/meta-product-catalog.xml', [\App\Http\Controllers\MetaCatalogController::class, 'productFeed'])->name('meta.product-feed');

// Bottom of file

