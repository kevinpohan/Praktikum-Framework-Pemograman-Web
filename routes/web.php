<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'RoleCheck:admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/product', action: [ProductController::class, 'index'])->name('product-index');
Route::get('/product/create', action: [ProductController::class, 'create'])->name('product-create');
Route::post('/product', action: [ProductController::class, 'store'])->name('product-store');
Route::get('/product/{id}', action: [ProductController::class, 'show'])->name("product-detail");
Route::get('/product/{id}/edit', action: [ProductController::class, 'edit'])->name('product-edit');
Route::put('/product/{id}', action: [ProductController::class, 'update'])->name('product-update');
Route::delete('/product/{id}', action: [ProductController::class, 'destroy'])->name('product-destroy');

Route::get('/product-export-excel', [ProductController::class, 'exportExcel'])->name('product.export.excel');

Route::get('/product-export-pdf', [ProductController::class, 'exportPDF'])->name('product.export.pdf');

Route::get('/product-export-jpg', [ProductController::class, 'exportJPG'])->name('product.export.jpg');



require __DIR__ . '/auth.php';
