<?php

use Illuminate\Support\Facades\Route;
use Iquesters\HelpSupport\Http\Controllers\UiController;

// Cached raw markdown file content — prevents direct browser calls to raw.githubusercontent.com
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/help-support/docs/file', [UiController::class, 'getDocFile'])
        ->name('helpsupport.docs.file');

    Route::get('/help-support/docs/files/{module}', [UiController::class, 'getModuleDocs'])
        ->name('helpsupport.docs.files');

    Route::get('/help-support/{viewName}', [UiController::class, 'show'])
        ->where('viewName', '[A-Za-z0-9._/-]+')
        ->name('helpsupport.ui.show');
});
