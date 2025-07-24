<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmigurumiPatternController;
use App\Http\Controllers\AmigurumiSectionController;
use App\Http\Controllers\AmigurumiRowController;
use App\Http\Controllers\ImageController;

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


    Route::resource('/amigurumi-patterns', AmigurumiPatternController::class);
    Route::apiResource('amigurumi-patterns.sections', AmigurumiSectionController::class);
    Route::apiResource('amigurumi-patterns.sections.rows', AmigurumiRowController::class);

    Route::post('/patterns/generate-pdf', [AmigurumiPatternController::class, 'generatePdf']);
    
    Route::post('/images/upload', [ImageController::class, 'upload'])->name('images.upload');
    Route::get('/api/patterns/{pattern}/images', [ImageController::class, 'amigurumiPattenIndex']);


    Route::delete('/images/{image}', [ImageController::class, 'deleteImage']);




});

require __DIR__.'/auth.php';
