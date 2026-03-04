<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\SuperAdmin\DashboardController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =========================================
// ============ SUPERADMIN ROUTES ==========
// =========================================


Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->group(function () {
    // This page only shows if you are logged in AND is_superadmin = true
     Route::get('/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');
     Route::get('/tenant/create', [TenantController::class, 'index'])->name('tenants.create');
     Route::post('/tenant/create', [TenantController::class, 'store'])->name('tenants.store');
     
   
});
require __DIR__.'/auth.php';
