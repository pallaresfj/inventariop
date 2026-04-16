<?php

use App\Http\Controllers\ItemImportFailureDownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'signed'])->group(function (): void {
    Route::get('/imports/items/failures/{file}', ItemImportFailureDownloadController::class)
        ->where('file', '[A-Za-z0-9\-_\.]+')
        ->name('items.import-failures.download');
});
