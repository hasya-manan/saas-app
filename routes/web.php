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

//::TODO :: does the superadmin can hop to user page ??
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->group(function () {
    // This page only shows if you are logged in AND is_superadmin = true
    //page dashboard superadmin
     Route::get('/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');
     
     // page create.vue
     Route::get('/tenant/create', [TenantController::class, 'index'])->name('tenants.create');
     Route::post('/tenant/create', [TenantController::class, 'store'])->name('tenants.store');

     // page list.vue
    Route::get('/tenant/list', [TenantController::class, 'list'])->name('tenants.list');
    // "Go to this specific tenant's ID and delete it"
    Route::delete('/tenant/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::put('/tenant/{id}/restore', [TenantController::class, 'restore'])->name('tenants.restore');
    // "Go to this specific deleted ID and wipe it forever"
    Route::delete('/tenant/{id}/force', [TenantController::class, 'forceDelete'])->name('tenants.force-delete');
    Route::put('/tenants/{id}', [TenantController::class, 'update'])->name('tenants.update');

    // page list.vue user
    Route::get('/users/list', [TenantController::class, 'userList'])->name('users.list');
     

});
require __DIR__.'/auth.php';
