<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $products = \App\Models\Product::latest()->take(3)->get();

    return view('welcome', compact('products'));
});

Route::get('/dashboard', function () {
    $totalProducts = \App\Models\Product::count();
    $totalStock = \App\Models\Product::sum('stock');
    $recentProducts = \App\Models\Product::latest()->take(5)->get();

    return view('dashboard', compact('totalProducts', 'totalStock', 'recentProducts'));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('products', ProductController::class);

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['index']);
        Route::resource('suppliers', SupplierController::class)->except(['index']);
    });

    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/clear', [\App\Http\Controllers\ChatController::class, 'clear'])->name('chat.clear');
});

require __DIR__.'/auth.php';
