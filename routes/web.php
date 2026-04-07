<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CallBackTripay;
use App\Http\Controllers\CertificateUsersController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\WebController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\CHPayController;
use App\Http\Controllers\Admin\HeroController;
use App\Http\Controllers\Admin\QAController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\AttendanceEventsList;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [HomeController::class, 'ListEvents'])->name('events.list');
Route::get('/events/{slug}', [HomeController::class, 'Show'])->name('events.show');

Route::get('/login', [LoginController::class, 'Login'])->name('login');
Route::post('/checkLogin', [LoginController::class, 'checkLogin'])->name('go.login');

// Certificate Generation
Route::get('/certificate', [CertificateUsersController::class, 'form'])->name('certificate.form');
Route::post('/certificate/get', [CertificateUsersController::class, 'generate'])->name('certificates.generate');

// Catch-all pages route (placed at the bottom to avoid route conflict)
Route::get('/{slug}', [HomeController::class, 'PagesShow'])->name('pages.show');

// Callback Route for Tripay
Route::post('/callback/tripay', [CallBackTripay::class, 'handle']);

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::get('/visitor/chart', [DashboardController::class, 'chartVisitor'])->name('admin.visitor.chart');
Route::prefix('rindutenang')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/home', [DashboardController::class, 'index'])->name('home');
        Route::prefix('user')
            ->name('user.')
            ->group(function () {
                Route::get('/', [UsersController::class, 'index'])->name('show');
                Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [UsersController::class, 'update'])->name('update');
            });
        Route::prefix('transaction')
            ->name('transaction.')
            ->group(function () {
                Route::get('/', [TransactionController::class, 'index'])->name('show');
                Route::get('/{orderID}/edit', [TransactionController::class, 'edit'])->name('edit');
                Route::post('/{orderID}/update', [TransactionController::class, 'update'])->name('update');
            });
        Route::prefix('events')
            ->name('events.')
            ->group(function () {
                Route::get('/', [EventController::class, 'index'])->name('show');
                Route::get('/create', [EventController::class, 'create'])->name('create');
                Route::get('/{eventsid}/show', [EventController::class, 'show'])->name('details');
                Route::get('/{eventsid}/show/edit', [EventController::class, 'edit'])->name('edit');
                Route::put('/{eventsid}/show/update', [EventController::class, 'update'])->name('update');
                Route::post('/events/{id}/settings', [EventController::class, 'updateSettings'])->name('updateSettings');
                Route::post('/create', [EventController::class, 'store'])->name('store');
                Route::get('/{id}/attendance', [AttendanceEventsList::class, 'index'])->name('absensi');
                Route::post('/{event}/attendance/{ticket}', [AttendanceEventsList::class, 'updateAttendance'])->name('attendance.update');
            });
        Route::prefix('certificate')
            ->name('certificate.')
            ->group(function () {
                Route::get('/', [CertificateController::class, 'index'])->name('show');
                Route::get('/create', [CertificateController::class, 'create'])->name('create');
                Route::post('/store', [CertificateController::class, 'store'])->name('store');
                Route::get('/{eventsid}/edit', [CertificateController::class, 'edit'])->name('edit');
                Route::put('/certificate/{id}', [CertificateController::class, 'update'])->name('update');
                Route::post('/certificate/{id}/preview', [CertificateController::class, 'preview'])->name('preview');
            });
        Route::prefix('hero')
            ->name('hero.')
            ->group(function () {
                Route::get('/', [HeroController::class, 'index'])->name('show');
                Route::get('/create', [HeroController::class, 'create'])->name('create');
                Route::post('/create', [HeroController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [HeroController::class, 'edit'])->name('edit');
                Route::put('/{id}/edit', [HeroController::class, 'update'])->name('update');
                Route::delete('/{id}', [HeroController::class, 'destroy'])->name('destroy');
            });
        Route::prefix('pages')
            ->name('pages.')
            ->group(function () {
                Route::get('/', [PagesController::class, 'index'])->name('show');
                Route::get('/create', [PagesController::class, 'create'])->name('create');
                Route::post('/create/store', [PagesController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [PagesController::class, 'edit'])->name('edit');
                Route::put('/{id}', [PagesController::class, 'update'])->name('update');
                Route::delete('/pages/{id}', [PagesController::class, 'destroy'])->name('destroy');
            });
        Route::prefix('qnas')
            ->name('qnas.')
            ->group(function () {
                Route::get('/', [QAController::class, 'index'])->name('show');
                Route::get('/create', [QAController::class, 'create'])->name('create');
                Route::post('/store', [QAController::class, 'store'])->name('store');
                Route::put('/{qna}', [QAController::class, 'update'])->name('update');
                Route::get('/{qna}/edit', [QAController::class, 'edit'])->name('edit');
                Route::delete('/{qna}/destroy', [QAController::class, 'destroy'])->name('destroy');
            });

        // Pengaturan Website
        Route::get('/webconfig', [WebController::class, 'index'])->name('webconfig');
        Route::post('/webconfig', [WebController::class, 'update'])->name('webconfig.update');
        // Pengaturan Channel Pembayaran
        Route::get('/channel', [CHPayController::class, 'index'])->name('channel.CHPay');
        Route::get('/channel/create', [CHPayController::class, 'create'])->name('channel.create');
        Route::get('/channel/{id}/edit', [CHPayController::class, 'edit'])->name('channel.edit');
        Route::post('/channel/store', [CHPayController::class, 'store'])->name('channel.store');
        Route::put('/channel/update/{id}', [CHPayController::class, 'update'])->name('channel.update');
    });

/*
|--------------------------------------------------------------------------
| Google OAuth Routes
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/oauth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dashboard & Profile
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::get('/user/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::get('/user/update-phone', [UserController::class, 'updateFormPhone'])->name('users.update.phone.form');
    Route::put('/profile/update-phone', [UserController::class, 'updatePhone'])->name('user.update.phone');

    // Order Detail
    Route::get('/orders/{orderID}/complate', [UserController::class, 'show'])->name('user.orders.details');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Order & Ticket Routes
    |--------------------------------------------------------------------------
    */

    // Order Creation & Payment Process
    Route::get('/events/{event}/orders', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/events/{event}/orders/validate', [OrderController::class, 'validateAttendees'])->name('orders.validate-attendees');
    Route::get('/events/{event}/orders/payment', [OrderController::class, 'paymentForm'])->name('orders.payment-form');
    Route::post('/events/{event}/orders/process-payment', [OrderController::class, 'processPayment'])->name('orders.process-payment');

    // Pay Now
    Route::get('/orders/{orderid}/details', [OrderController::class, 'paynow'])->name('orders.pay');

    // Order Completion
    Route::get('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');

    // Order Cancellation
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    
    // Ticket Download
    // Route::get('/tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');
    Route::get('/tickets/{order}/download', [TicketController::class, 'downloadAll'])->name('tickets.downloads');
});
