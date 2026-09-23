<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{AuthController, CategoryController, ProductController, SettingController, DashboardController, UsersController, OrderController as AdminOrderController};
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\{AppointmentController, CartController, WishlistController, UserController, FrontendController, OrderController, ReviewController};
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\AdminController;

Route::middleware(['web'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ÖN YÜZ (FRONTEND) ROTALARI
    |--------------------------------------------------------------------------
    */
    Route::get('/', [FrontendController::class, 'index'])->name('home');
    Route::get('products', [FrontendController::class, 'products'])->name('products');
    Route::get('about', [FrontendController::class, 'about'])->name('about');
    Route::get('appointment', [FrontendController::class, 'appointment'])->name('appointment');
    Route::get('product/{id}', [FrontendController::class, 'show'])->name('product.show');
    Route::get('/product/detail/{id}', [ProductController::class, 'showDetail']);
    Route::get('contact', [FrontendController::class, 'contact'])->name('contact');
    Route::post('contact/store', [FrontendController::class, 'contactStore'])->name('contact.store');
    Route::post('/appointment/store', [AppointmentController::class, 'store'])->name('appointment.store');

    Route::post('cart/store', [CartController::class, 'store'])->name('cart.store')->middleware('auth');
    Route::post('/cart/add', [CartController::class, 'store'])->middleware('auth');

    Route::get('login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('login', [UserController::class, 'login']);
    Route::post('register', [UserController::class, 'register'])->name('register');
    Route::post('logout', [UserController::class, 'logout'])->name('logout');

    // MÜŞTERİ ŞİFREMİ UNUTTUM MODAL ROTALARI
    Route::post('password/send-code', [ForgotPasswordController::class, 'sendResetCode'])->name('password.send-code');
    Route::post('password/update-with-code', [ForgotPasswordController::class, 'resetPassword'])->name('password.update-with-code');

    Route::resource('product-groups', App\Http\Controllers\Admin\ProductGroupController::class);

    /*
    |--------------------------------------------------------------------------
    | GİRİŞ YAPMIŞ KULLANICI (DASHBOARD) ROTALARI
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->prefix('dashboard')->group(function () {
        Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('profil', [UserController::class, 'profile'])->name('dashboard.profile');
        Route::put('profil/guncelle', [UserController::class, 'updateProfile'])->name('dashboard.profile.update');
        
        // Favoriler
        Route::get('favorilerim', [WishlistController::class, 'index'])->name('dashboard.wishlist');
        Route::get('/wishlist/status/{productId}', [WishlistController::class, 'checkStatus'])->name('wishlist.status');
        Route::post('wishlist/store', [WishlistController::class, 'store'])->name('wishlist.store');
        Route::post('wishlist/remove/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
        Route::post('wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

        // Sepet
        Route::get('sepetim', [CartController::class, 'index'])->name('dashboard.cart'); 
        Route::post('cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::post('cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('cart/move-to-wishlist/{id}', [CartController::class, 'moveToWishlist'])->name('cart.moveToWishlist');
        Route::get('sepet/onayla', [CartController::class, 'checkout'])->name('checkout');
        Route::post('sepet/siparis-tamamla', [CartController::class, 'storeOrder'])->name('order.store');
        Route::post('/cart', [CartController::class, 'store'])->middleware('auth');

        // Siparişler (Müşteri Tarafı)
        Route::get('hesabim/siparislerim', [OrderController::class, 'myOrders'])->name('orders.index');
        Route::get('/order/success/{id}', [OrderController::class, 'success'])->name('order.success');

        Route::middleware(['auth'])->group(function () {
            Route::post('/order/store', [App\Http\Controllers\CartController::class, 'storeOrder'])->name('order.store');
            Route::get('/order/success', [App\Http\Controllers\CartController::class, 'orderSuccess'])->name('order.success');
        });

        // Yasal Sayfalar
        Route::get('/kvkk', function () { return view('legal.kvkk'); });
        Route::get('/satis-sozlesmesi', function () { return view('legal.sozlesme'); });
    });

    /*
    |--------------------------------------------------------------------------
    | ADMİN PANELİ ROTALARI
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('login', [AuthController::class, 'login'])->name('admin.login.post');
        Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

        // Yönetici Yetkisi Gerektiren Rotalar
        Route::middleware('auth:admin')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
            
           // Satış Yönetimi Rotaları
            Route::get('orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
            Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
            Route::put('orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

            Route::resource('categories', CategoryController::class)->names(['index' => 'categories.index']);
            Route::resource('products', ProductController::class)->names(['index' => 'products.index']);
            
            Route::post('/product/comment/store', [ReviewController::class, 'store'])->name('product.comment.store')->middleware('auth');
            Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
            Route::get('appointments', [AdminAppointmentController::class, 'index'])->name('admin.appointments.index');
            Route::patch('appointments/{id}/status', [AdminAppointmentController::class, 'updateStatus'])->name('admin.appointments.updateStatus');
            Route::delete('appointments/{id}', [AdminAppointmentController::class, 'destroy'])->name('admin.appointments.destroy');

            Route::get('settings', [SettingController::class, 'index'])->name('admin.settings.index');
            Route::put('settings', [SettingController::class, 'update'])->name('admin.settings.update');
            
            // İletişim Mesajları Rotaları
            Route::get('contacts', [AdminContactController::class, 'index'])->name('admin.contact.index');
            Route::get('contacts', [AdminContactController::class, 'index'])->name('admin.contacts.index'); // Her iki ismi de destekler
            Route::delete('contacts/{id}', [AdminContactController::class, 'destroy'])->name('admin.contact.destroy');
            
            Route::get('users', [UsersController::class, 'index'])->name('users.index');
            Route::patch('users/{id}/toggle', [UsersController::class, 'toggleStatus'])->name('admin.user.toggle');
            Route::delete('users/{id}', [UsersController::class, 'destroy'])->name('admin.user.destroy');
            
            // Admin Yönetimi Rotaları
            Route::prefix('admins')->group(function () {
                Route::get('/', [AdminController::class, 'index'])->name('admins.index');
                Route::post('/', [AdminController::class, 'store'])->name('admins.store');
                Route::put('/{id}/password', [AdminController::class, 'updatePassword'])->name('admins.update-password');
                Route::delete('/{id}', [AdminController::class, 'destroy'])->name('admins.destroy');
            });
        });
    });
});