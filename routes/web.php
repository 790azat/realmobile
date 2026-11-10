<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ShopController;
use App\Http\Middleware\AdminAuth;
use App\Livewire;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/iphone', Livewire\Catalog::class)->name('catalog');
Route::get('/iphone/{slug}', Livewire\ProductPage::class)->name('product');
Route::get('/accessories', [ShopController::class, 'accessories'])->name('accessories');
Route::get('/accessories/{slug}', [ShopController::class, 'accessories'])->name('accessories.category');
Route::get('/img-status', [ImageController::class, 'status']);
Route::get('/img/{key}', [ImageController::class, 'show'])->where('key', '[a-z0-9_\-]+')->name('img');
Route::get('/sitemap.xml', [ShopController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [ShopController::class, 'robots']);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'show'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(AdminAuth::class)->group(function () {
        Route::get('/', Livewire\Admin\Dashboard::class)->name('dashboard');
        Route::get('leads', Livewire\Admin\Leads::class)->name('leads');
        Route::get('products', Livewire\Admin\Products::class)->name('products');
        Route::get('products/create', Livewire\Admin\ProductForm::class)->name('products.create');
        Route::get('products/{product}/edit', Livewire\Admin\ProductForm::class)->name('products.edit');
        Route::get('categories', Livewire\Admin\Categories::class)->name('categories');
    });
});
