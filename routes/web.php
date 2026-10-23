<?php

use App\Http\Controllers\PublicEpaperController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| VSP TIMES E-Paper Portal Routes (epaper.vspmedianetwork.com)
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicEpaperController::class, 'index'])->name('epaper.home');
Route::get('/edition/{edition}', [PublicEpaperController::class, 'reader'])->name('epaper.edition');
Route::get('/edition/{edition}/page/{pageNumber?}', [PublicEpaperController::class, 'reader'])->name('epaper.reader');
Route::get('/archive', [PublicEpaperController::class, 'archive'])->name('epaper.archive');
Route::get('/edition/{edition}/download', [PublicEpaperController::class, 'downloadPdf'])->name('epaper.download');
