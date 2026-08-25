<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ContactInfoController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ChargesController;
use App\Http\Controllers\API\AppSliderController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Custom login route for mobile app

// API Routes with token middleware
Route::middleware('api.token')->group(function () {
    
    // Contact Info
    Route::get('/getContactInfo', [ContactInfoController::class, 'getContactInfo']);
    
    // App Slider
    Route::get('/getAllSliders', [AppSliderController::class, 'getAllSliders']);
    
    // validateUser - for mobile number check & OTP
    Route::post('/validateUser', [AuthController::class, 'validateUser']);
    
    // userLogin - login customer using mobile + otp
    Route::post('/userLogin', [AuthController::class, 'userLogin']);
    
    // userRegister - register customer
    Route::post('/userRegister', [AuthController::class, 'userRegister']);
    
    // getStates - send all state name (static or db)
    Route::get('/getStates', [LocationController::class, 'getStates']);
    
    // getProductCategories - send all categories
    Route::get('/getProductCategories', [ProductCategoryController::class, 'getProductCategories']);
    
    // getProducts - all, tag-wise OR category-wise
    Route::get('/getProducts', [ProductController::class, 'getProducts']);
    
    // getProductDetails - details of one product
    Route::get('/getProductDetails/{id}', [ProductController::class, 'getProductDetails']);
    
    //checkTokenValidity - for Autologin
    Route::post('/checkTokenValidity', [AuthController::class, 'checkTokenValidity']);
    
});

Route::middleware(['api.token', 'customer.auth'])->group(function () {

    // CART
    Route::get('/getCartData', [CartController::class, 'getCartData']);
    Route::post('/setCartData', [CartController::class, 'setCartData']);
    Route::post('/deleteCartItem', [CartController::class, 'deleteCartItem']);


    // CUSTOMER PROFILE
    Route::get('/customer/profile', [CustomerController::class, 'getCustomerProfile']);
    Route::post('/customer/profile/update', [CustomerController::class, 'updateCustomerProfile']);

    // CHARGES
    Route::get('/getCharges', [ChargesController::class, 'getCharges']);

    // ORDER
    Route::post('/order/place', [OrderController::class, 'placeOrder']);
    Route::post('/order/confirm', [OrderController::class, 'confirmOrder']);
    Route::get('/getOrders', [OrderController::class, 'getOrders']);
    Route::get('/getOrderDetails/{order_id}', [OrderController::class, 'getOrderDetails']);
    Route::post('/order/update', [OrderController::class, 'updateOrder']);
});


