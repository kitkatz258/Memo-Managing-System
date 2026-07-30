<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function(){
    Route::get('/memos', [MemoController::class, 'index'])->name('memos.index');
    Route::post('/memos', [MemoController::class, 'store'])->name('memos.store');
    Route::get('/memos/{memo}', [MemoController::class, 'show'])->name('memos.show');
    Route::delete('/memos/{memo}', [MemoController::class, 'archive'])->name('memos.archive');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';