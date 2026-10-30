<?php

use App\Http\Controllers\SectorFilesController;
use Illuminate\Support\Facades\Route;

Route::get('/atc/sector-files', [SectorFilesController::class, 'index'])->name('sector-files.index');

Route::prefix('dashboard/staff/sector-files')->middleware(['auth', 'staff'])->group(function () {
    Route::get('/', [SectorFilesController::class, 'manage'])->name('sector-files.manage');
    Route::post('/', [SectorFilesController::class, 'store'])->middleware('throttle:10,1')->name('sector-files.store');
    Route::post('/{release}/publish', [SectorFilesController::class, 'publish'])->whereNumber('release')->name('sector-files.publish');
    Route::patch('/{release}', [SectorFilesController::class, 'update'])->whereNumber('release')->name('sector-files.update');
    Route::patch('/{release}/archive', [SectorFilesController::class, 'archive'])->whereNumber('release')->name('sector-files.archive');
    Route::delete('/{release}', [SectorFilesController::class, 'destroy'])->whereNumber('release')->name('sector-files.destroy');
});
