<?php

use App\Http\Controllers\OfficeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Offices Management — all authenticated users
    // Live View (API)
    Route::get('/offices', [OfficeController::class, 'index'])->name('offices.index');
    
    // Super Admin Management (Local DB)
    Route::middleware('role:Super Admin')->group(function () {
        Route::get('/offices/manage', [OfficeController::class, 'manage'])->name('offices.manage');
        Route::post('/offices', [OfficeController::class, 'store'])->name('offices.store');
        Route::put('/offices/{office}', [OfficeController::class, 'update'])->name('offices.update');
        Route::delete('/offices/{office}', [OfficeController::class, 'destroy'])->name('offices.destroy');
        Route::post('/offices/sync', [OfficeController::class, 'sync'])->name('offices.sync');
    });

    Route::get('/offices/{id}/employees', [OfficeController::class, 'employees'])->name('offices.employees');

    // User Management — Super Admin and Admin only
    Route::middleware('role:Super Admin|Admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Employee Management (Proxy to iHRIS)
        Route::post('/employees', [App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
        Route::put('/employees/{id}', [App\Http\Controllers\EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{id}', [App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

});
