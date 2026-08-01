<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemoController;
use App\Models\Memo;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function(){
    Route::get('/memos', [MemoController::class, 'index'])->name('memos.index');
    Route::get('/memos/archived', [MemoController::class, 'archived'])->name('memos.archived');
    Route::patch('/memos/{id}/restore', [MemoController::class, 'restore'])->name('memos.restore');
    Route::get('/memos/{memo}', [MemoController::class, 'show'])->name('memos.show');
    Route::delete('/memos/{memo}', [MemoController::class, 'archive'])->name('memos.archive');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/memos/search-picker', [MemoController::class, 'searchPicker'])->name('memos.search-picker');
Route::get('/memos/{memo}/view', [MemoController::class, 'viewInline'])->name('memos.view');
Route::get('/memos/{memo}/download', [MemoController::class, 'download'])->name('memos.download');

require __DIR__.'/auth.php';