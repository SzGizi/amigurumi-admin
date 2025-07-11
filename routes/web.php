<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmigurumiPatternController;
use App\Http\Controllers\AmigurumiSectionController;
use App\Http\Controllers\AmigurumiRowController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Amigurumi edit 

    Route::resource('/amigurumi-patterns', AmigurumiPatternController::class);
    //Route::apiResource('/amigurumi-patterns', AmigurumiPatternController::class);
    Route::apiResource('amigurumi-patterns.sections', AmigurumiSectionController::class);
    Route::apiResource('amigurumi-patterns.sections.rows', AmigurumiRowController::class);
});

require __DIR__.'/auth.php';
