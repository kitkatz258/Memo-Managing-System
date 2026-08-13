<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemoController;
use App\Models\Memo;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\UserMemoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');


// =========================
// ADMIN ONLY
// =========================

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/memos', [MemoController::class, 'index'])
        ->name('memos.index');
    Route::get('/memos/archived', [MemoController::class, 'archived'])
        ->name('memos.archived');
    Route::get('/memos/search-picker', [MemoController::class, 'searchPicker'])
        ->name('memos.search-picker');
    Route::get('/admin/users', [UserManagementController::class, 'index'])
        ->name('admin.users.index');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])
        ->name('admin.users.destroy');
    Route::patch('/memos/{id}/restore', [MemoController::class, 'restore'])
        ->name('memos.restore');
    Route::get('/memos/{id}/logs', [MemoController::class, 'logs'])
        ->name('memos.logs');
    Route::post('/memos/{id}/archive', [MemoController::class, 'archive'])
        ->name('memos.archive');
});


// =========================
// AUTHENTICATED USERS
// =========================

Route::middleware(['auth'])->group(function () {

    Route::get('/memos/{memo}/details', [MemoController::class, 'details'])
        ->withTrashed()
        ->name('memos.details');
    Route::get('/memos/{memo}/view', [MemoController::class, 'viewInline'])
        ->withTrashed()
        ->name('memos.view');
    Route::get('/memos/{memo}/download', [MemoController::class, 'download'])
        ->withTrashed()
        ->name('memos.download');
    Route::get('/user/memos', [UserMemoController::class, 'index'])
        ->name('user.memos.index');

    // Route::get('/profile', [ProfileController::class, 'edit'])
    //     ->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])
    //     ->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])
    //     ->name('profile.destroy');
});


require __DIR__.'/auth.php';