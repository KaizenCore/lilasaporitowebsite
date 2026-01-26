<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClassController as AdminClassController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ClassCartController;
use App\Http\Controllers\ClassCheckoutController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PartyInquiryController;
use App\Http\Controllers\PartyCheckoutController;
use App\Http\Controllers\Admin\PartyPaintingController;
use App\Http\Controllers\Admin\PartyPricingController;
use App\Http\Controllers\Admin\PartyAddonController;
use App\Http\Controllers\Admin\PartyAvailabilityController;
use App\Http\Controllers\Admin\PartyBookingController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
Route::get('/classes/{slug}', [ClassController::class, 'show'])->name('classes.show');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/policy', [PolicyController::class, 'index'])->name('policy');

// Public Party Routes
Route::prefix('parties')->name('parties.')->group(function () {
    Route::get('/', [PartyController::class, 'index'])->name('index');
    Route::get('/paintings', [PartyController::class, 'paintings'])->name('paintings');
    Route::get('/api/available-dates', [PartyController::class, 'availableDates'])->name('api.available-dates');
    Route::post('/api/pricing-estimate', [PartyController::class, 'getPricingEstimate'])->name('api.pricing-estimate');

    // Auth required for inquiry and booking
    Route::middleware('auth')->group(function () {
        Route::get('/inquire', [PartyInquiryController::class, 'create'])->name('inquire');
        Route::post('/inquire', [PartyInquiryController::class, 'store'])->name('inquire.store');
        Route::get('/my-inquiries', [PartyInquiryController::class, 'index'])->name('my-inquiries');
        Route::get('/booking/{partyBooking}', [PartyInquiryController::class, 'show'])->name('booking.show');

        // Checkout/Payment
        Route::get('/checkout/{partyBooking}', [PartyCheckoutController::class, 'show'])->name('checkout');
        Route::post('/checkout/{partyBooking}/accept', [PartyCheckoutController::class, 'acceptQuote'])->name('checkout.accept');
        Route::post('/checkout/{partyBooking}/payment-intent', [PartyCheckoutController::class, 'createPaymentIntent'])->middleware('throttle:10,1')->name('checkout.payment-intent');
        Route::get('/checkout/{partyBooking}/success', [PartyCheckoutController::class, 'success'])->name('checkout.success');
    });
});

// API Route for Party Payment Status (Auth Required)
Route::middleware('auth')->get('/api/check-party-payment/{paymentIntentId}', [PartyCheckoutController::class, 'checkPaymentStatus'])->name('api.check-party-payment');

// Store Routes
Route::get('/store', [StoreController::class, 'index'])->name('store.index');
Route::get('/store/category/{slug}', [StoreController::class, 'category'])->name('store.category');
Route::get('/store/{slug}', [StoreController::class, 'show'])->name('store.show');

// Google OAuth Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Admin Login Routes (Separate password-based authentication)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'show'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->middleware('throttle:5,1')->name('admin.login.submit');
});

// Dashboard (Auth Required)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// User Bookings (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// User Orders (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// Order Checkout Routes (Auth Required) - Must be before wildcard /checkout/{class:slug}
Route::middleware('auth')->group(function () {
    Route::get('/checkout/order', [OrderController::class, 'checkout'])->name('checkout.order');
    Route::post('/checkout/order/payment-intent', [OrderController::class, 'createPaymentIntent'])->middleware('throttle:10,1')->name('checkout.order.payment-intent');
    Route::get('/checkout/order/success/{order}', [OrderController::class, 'success'])->name('checkout.order.success');
});

// Class Checkout Routes (Auth Required) - Must be before wildcard /checkout/{class:slug}
Route::middleware('auth')->group(function () {
    Route::get('/checkout/classes', [ClassCheckoutController::class, 'checkout'])->name('checkout.classes');
    Route::post('/checkout/classes/payment-intent', [ClassCheckoutController::class, 'createPaymentIntent'])->middleware('throttle:10,1')->name('checkout.classes.payment-intent');
    Route::get('/checkout/classes/success/{order}', [ClassCheckoutController::class, 'success'])->name('checkout.classes.success');
});

// Single Class Checkout & Payment Routes (Auth Required) - Wildcard must be LAST
Route::middleware('auth')->group(function () {
    Route::get('/checkout/{class:slug}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/payment-intent', [CheckoutController::class, 'createPaymentIntent'])->middleware('throttle:10,1')->name('checkout.payment-intent');
    Route::post('/checkout/confirm-payment', [CheckoutController::class, 'confirmPayment'])->middleware('throttle:10,1')->name('checkout.confirm-payment');
    Route::get('/checkout/success/{booking}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Cart Routes (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// Class Cart Routes (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/class-cart', [ClassCartController::class, 'index'])->name('class-cart.index');
    Route::post('/class-cart/add', [ClassCartController::class, 'add'])->name('class-cart.add');
    Route::delete('/class-cart/remove/{artClassId}', [ClassCartController::class, 'remove'])->name('class-cart.remove');
    Route::delete('/class-cart/clear', [ClassCartController::class, 'clear'])->name('class-cart.clear');
    Route::get('/class-cart/count', [ClassCartController::class, 'count'])->name('class-cart.count');
});

// API Route for Class Order Status (Auth Required)
Route::middleware('auth')->get('/api/check-class-order/{paymentIntentId}', [ClassCheckoutController::class, 'checkOrderStatus'])->name('api.check-class-order');

// Digital Download Route (Auth Required)
Route::middleware('auth')->get('/download/{token}', [DownloadController::class, 'download'])->name('download');

// API Routes for Order Status (Auth Required)
Route::middleware('auth')->get('/api/check-order/{paymentIntentId}', [OrderController::class, 'checkOrderStatus'])->name('api.check-order');

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
    Route::get('/classes/{class}/recurring', [AdminClassController::class, 'showRecurringForm'])->name('classes.recurring');
    Route::post('/classes/{class}/recurring/preview', [AdminClassController::class, 'previewRecurring'])->name('classes.recurring.preview');
    Route::post('/classes/{class}/recurring', [AdminClassController::class, 'generateRecurring'])->name('classes.recurring.generate');

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

    // Site Settings
    Route::get('/settings', [SiteSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SiteSettingsController::class, 'update'])->name('settings.update');
    Route::delete('/settings/photo', [SiteSettingsController::class, 'deletePhoto'])->name('settings.delete-photo');

    // Reports & Export
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportsController::class, 'exportPayments'])->name('reports.export');
    Route::get('/reports/revenue/pdf', [ReportsController::class, 'revenuePdf'])->name('reports.revenue.pdf');
    Route::post('/reports/revenue/email', [ReportsController::class, 'emailRevenue'])->name('reports.revenue.email');
    Route::get('/reports/bookings', [ReportsController::class, 'bookings'])->name('reports.bookings');
    Route::get('/reports/bookings/export', [ReportsController::class, 'exportBookings'])->name('reports.bookings.export');
    Route::get('/reports/bookings/pdf', [ReportsController::class, 'exportBookingsPdf'])->name('reports.bookings.pdf');
    Route::get('/reports/attendance', [ReportsController::class, 'attendance'])->name('reports.attendance');
    Route::get('/reports/attendance/{artClass}/pdf', [ReportsController::class, 'attendancePdf'])->name('reports.attendance.pdf');

    // Party Management
    Route::prefix('parties')->name('parties.')->group(function () {
        // Paintings Gallery
        Route::resource('paintings', PartyPaintingController::class);

        // Pricing Configs
        Route::resource('pricing', PartyPricingController::class);

        // Add-ons
        Route::resource('addons', PartyAddonController::class);

        // Availability Management
        Route::get('/availability', [PartyAvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability', [PartyAvailabilityController::class, 'store'])->name('availability.store');
        Route::post('/availability/bulk', [PartyAvailabilityController::class, 'bulkStore'])->name('availability.bulk');
        Route::delete('/availability/{slot}', [PartyAvailabilityController::class, 'destroy'])->name('availability.destroy');
        Route::post('/availability/blackout', [PartyAvailabilityController::class, 'blackout'])->name('availability.blackout');
        Route::post('/availability/unblock', [PartyAvailabilityController::class, 'unblock'])->name('availability.unblock');
        Route::get('/availability/data', [PartyAvailabilityController::class, 'getData'])->name('availability.data');

        // Booking Management
        Route::get('/bookings', [PartyBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{partyBooking}', [PartyBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{partyBooking}/quote', [PartyBookingController::class, 'sendQuote'])->name('bookings.quote');
        Route::post('/bookings/{partyBooking}/confirm', [PartyBookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('/bookings/{partyBooking}/decline', [PartyBookingController::class, 'decline'])->name('bookings.decline');
        Route::post('/bookings/{partyBooking}/complete', [PartyBookingController::class, 'complete'])->name('bookings.complete');
        Route::post('/bookings/{partyBooking}/cancel', [PartyBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('/bookings/{partyBooking}/notes', [PartyBookingController::class, 'addNotes'])->name('bookings.notes');
    });
});

require __DIR__.'/auth.php';
