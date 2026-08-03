<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockAlertController;
use App\Http\Controllers\StockMovementController;
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
    $inventoryValue = round(\App\Models\Product::selectRaw('SUM(stock * price) as total')->value('total') ?? 0, 2);
    $lowStockCount = \App\Models\StockAlert::open()->count();
    $totalSuppliers = \App\Models\Supplier::count();
    $totalCategories = \App\Models\Category::count();
    $recentProducts = \App\Models\Product::latest()->take(5)->get();
    $openAlerts = \App\Models\StockAlert::with('product')->open()->latest()->take(5)->get();
    $recentMovements = \App\Models\StockMovement::with(['product', 'user'])->latest()->take(5)->get();

    return view('dashboard', compact(
        'totalProducts', 'totalStock', 'inventoryValue', 'lowStockCount',
        'totalSuppliers', 'totalCategories', 'recentProducts', 'openAlerts', 'recentMovements'
    ));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('products', ProductController::class);

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

    Route::get('stock', [StockMovementController::class, 'index'])->name('stock.index');
    Route::get('stock/create', [StockMovementController::class, 'create'])->name('stock.create');
    Route::post('stock', [StockMovementController::class, 'store'])->name('stock.store');
    Route::delete('stock/{stockMovement}', [StockMovementController::class, 'destroy'])->name('stock.destroy');

    Route::get('alerts', [StockAlertController::class, 'index'])->name('alerts.index');
    Route::post('alerts/{stockAlert}/resolve', [StockAlertController::class, 'resolve'])->name('alerts.resolve');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/valuation', [ReportController::class, 'valuation'])->name('reports.valuation');
    Route::get('reports/stock-levels', [ReportController::class, 'stockLevels'])->name('reports.stock_levels');
    Route::get('reports/movements', [ReportController::class, 'movements'])->name('reports.movements');
    Route::get('reports/suppliers', [ReportController::class, 'suppliers'])->name('reports.suppliers');
    Route::get('reports/categories', [ReportController::class, 'categories'])->name('reports.categories');
    Route::get('reports/export/valuation', [ReportController::class, 'exportValuation'])->name('reports.export_valuation');
    Route::get('reports/export/stock-levels', [ReportController::class, 'exportStockLevels'])->name('reports.export_stock_levels');
    Route::get('reports/export/suppliers', [ReportController::class, 'exportSuppliers'])->name('reports.export_suppliers');
    Route::get('reports/export/categories', [ReportController::class, 'exportCategories'])->name('reports.export_categories');

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
