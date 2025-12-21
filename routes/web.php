<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClassController as AdminClassController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
Route::get('/classes/{slug}', [ClassController::class, 'show'])->name('classes.show');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Store Routes
Route::get('/store', [StoreController::class, 'index'])->name('store.index');
Route::get('/store/category/{slug}', [StoreController::class, 'category'])->name('store.category');
Route::get('/store/{slug}', [StoreController::class, 'show'])->name('store.show');

// Dashboard (Auth Required)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// User Bookings (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');
});

// Checkout & Payment Routes (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/checkout/{class:slug}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/payment-intent', [CheckoutController::class, 'createPaymentIntent'])->name('checkout.payment-intent');
    Route::get('/checkout/success/{booking}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Stripe Webhook (No Auth, No CSRF)
Route::post('/webhook/stripe', [PaymentController::class, 'webhook'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Profile Management (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Class Management
    Route::resource('classes', AdminClassController::class);

    // Booking Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/check-in', [AdminBookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::post('/bookings/{booking}/manual-check-in', [AdminBookingController::class, 'manualCheckIn'])->name('bookings.manual-check-in');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/classes/{class}/check-in', [AdminBookingController::class, 'checkInForm'])->name('classes.check-in-form');

    // Calendar View
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/data', [CalendarController::class, 'getData'])->name('calendar.data');
    Route::get('/calendar/class/{class}', [CalendarController::class, 'quickView'])->name('calendar.quick-view');

    // Product Management
    Route::resource('products', ProductController::class);
    Route::resource('categories', ProductCategoryController::class);
});

require __DIR__.'/auth.php';
